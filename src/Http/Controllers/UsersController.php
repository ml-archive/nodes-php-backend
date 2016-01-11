<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Nodes\Backend\Models\Role\RoleRepository;
use Nodes\Backend\Models\User\UserRepository;
use Nodes\Backend\Models\User\Validation\UserValidator;

/**
 * Class UsersController
 *
 * @package Nodes\Backend\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * User Repository
     *
     * @var \Nodes\Backend\Models\User\UserRepository
     */
    protected $userRepository;

    /**
     * Role repository
     *
     * @var \Nodes\Backend\Models\Role\RoleRepository
     */
    protected $roleRepository;

    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Models\Role\RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->userRepository = app(config('nodes.backend.auth.repository'));
        $this->roleRepository = $roleRepository;
    }

    /**
     * List all users
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check user level
        if(\Gate::denies('admin')) {
            abort(403);
        }

        // Run query restorer
        if($route = query_restorer([], ['search'])) {
            return redirect()->to($route);
        }

        // Retrieve all users
        $users = $this->userRepository->getPaginated();

        return view('nodes.backend::backend-users.index', compact('users'));
    }

    /**
     * Create new user form
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Check user level
        if(\Gate::denies('admin')) {
            abort(403);
        }

        // Retrieve available roles for users user-role
        $roles = $this->roleRepository->getListUserLevel();

        // Retrieve default role name
        $roleDefault = $this->roleRepository->getDefaultRole()->slug;

        return view('nodes.backend::backend-users.edit', compact('roles', 'roleDefault'));
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Nodes\Backend\Models\User\Validation\UserValidator $userValidator
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(UserValidator $userValidator)
    {
        // Check user level
        if(\Gate::denies('admin')) {
            abort(403);
        }

        // Retrieve posted data
        $data = \Input::all();

        // Random a password if it's left empty
        if(empty($data['password'])) {
            $data['password'] = $data['password_confirmation'] = Str::random(8);
        }

        // Validate
        if(!$userValidator->with($data)->validate()) {
            return redirect()->back()->withInput()->with(['error' => $userValidator->errorsBag()]);
        }

        try {
            // Create user
            $user = $this->userRepository->createUser($data);

            // Send a email with information
            if(filter_var($data['send_mail'], FILTER_VALIDATE_BOOLEAN)) {
                $this->userRepository->sendWelcomeMail($user, $data['password']);
            }

            return redirect()->route('nodes.backend.users')->with('success', 'User was successfully created');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Could not create user');
        }
    }

    /**
     * Edit user form
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Retrieve user by ID
        $user = $this->userRepository->getById($id);
        if (!$user) {
            return redirect()->route('nodes.backend.users')->with('error', 'User was not found');
        }

        // Make sure user has access to edit this user
        if(\Gate::denies('edit-user', $user)) {
            abort(403);
        }

        // Retrieve available roles for users user-role
        $roles = $this->roleRepository->getListUserLevel();

        return view('nodes.backend::backend-users.edit', compact('user', 'roles'));
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Nodes\Backend\Models\User\Validation\UserValidator $userValidator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserValidator $userValidator)
    {
        // Retrieve posted data
        $data = \Input::get();

        // Retrieve user to update
        $user = $this->userRepository->getById($data['id']);
        if (!$user) {
            return redirect()->route('nodes.backend.users')->with('error', 'User was not found');
        }

        // Make sure user has access to edit this user
        if(\Gate::denies('edit-user', $user)) {
            abort(403);
        }

        // Validate
        if(!$userValidator->with($data)->validate()) {
            return redirect()->back()->withInput()->with(['error' => $userValidator->errorsBag()]);
        }

        // Retrieve available roles for users user-role, and make sure that the selected role is within the access level
        // of the authed user, else unset the role
        $roles = $this->roleRepository->getListUserLevel();
        if(!isset($roles[$data['user_role']])) {
            unset($data['user_role']);
        }

        // Update user and redirect
        try {
            $this->userRepository->updateUser($user, $data);

            // Only admins have access to list of users, users need to go to
            if(\Gate::allows('admin')) {
                return redirect()->route('nodes.backend.users')->with('success', 'User was successfully updated');
            } else {
                return redirect()->route(config('nodes.backend.auth.routes.success'))->with('success', 'User was successfully updated');
            }

        } catch(\Exception $e) {
            dd($e);
            return redirect()->back()->withInput()->with('error', 'Could not update user');
        }
    }

    /**
     * Edit authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        return $this->edit(backend_user()->id);
    }

    /**
     * Delete user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @param  integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // Authenticated user
        $authedUser = \NodesBackend::user();

        // Validate permissions
        if (!$authedUser->isNodesOrAdmin()) {
            return redirect()->route('nodes.backend.errors.permission-denied');
        }

        // Retrieve user we're about to delete
        $user = $this->userRepository->getById($id);
        if (empty($user)) {
            return redirect()->route('nodes.backend.users.list')->with([
                'error' => 'The user you are trying delete does not exist.'
            ]);
        }

        // Make sure we're not trying to delete a Nodes user
        if ($user->isNodes()) {
            return redirect()->route('nodes.backend.users.list')->with([
                'error' => 'Sorry. It is not possible to delete the Nodes user.'
            ]);
        }

        // Make sure user is not trying delete him-/herself
        if ($user->id == \NodesBackend::user()->id) {
            return redirect()->route('nodes.backend.users.list')->with('error', 'Sorry. You can not delete yourself.');
        }

        if (!$user->delete()) {
            return redirect()->back()->with('error', 'Could not delete user. Try again or contact an administrator.');
        } else {
            return redirect()->route('nodes.backend.users.list')->with('success', 'User was successfully deleted.');
        }
    }

    /**
     * Change password form
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\View\View
     */
    public function changePassword()
    {
        return view('nodes.backend::backend-users.change-password');
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Nodes\Backend\Models\User\Validation\UserValidator $userValidator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UserValidator $userValidator)
    {
        // Retrieve posted data
        $data = \Input::get();

        // Retrieve user to update
        $user = $this->userRepository->getById($data['id']);
        if (!$user || $user->id != backend_user()->id) {
            return redirect()->route('nodes.backend.users')->with('error', 'User was not found');
        }

        // Validate
        if(!$userValidator->with($data)->group('update-password')->validate()) {
            return redirect()->back()->withInput()->with(['error' => $userValidator->errorsBag()]);
        }

        // Set the state back
        $data['change_password'] = false;

        // Update user and redirect
        try {
            $this->userRepository->updateUser($user, $data);

            return redirect()->route('nodes.backend.dashboard')->with('success', 'Password is updated');
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Could not update password');
        }
    }
}
