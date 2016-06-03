<?php
namespace Nodes\Backend\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Nodes\Backend\Models\Role\RoleRepository;
use Nodes\Backend\Models\Role\Validation\RoleValidator;

/**
 * Class UsersRolesController
 *
 * @package Nodes\Backend\Http\Controllers
 */
class RolesController extends Controller
{
    /**
     * Role repository
     *
     * @var \Nodes\Backend\Models\Role\RoleRepository
     */
    protected $roleRepository;

    /**
     * Constructor
     *
     * @access public
     * @param  \Nodes\Backend\Models\Role\RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        if (Gate::denies('backend-developer')) {
            abort(403);
        }

        $this->roleRepository = $roleRepository;
    }

    /**
     * List all roles
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all roles
        $roles = $this->roleRepository->getPaginatedForBackend();

        return view('nodes.backend::backend-users.roles', compact('roles'));
    }

    /**
     * Save new role to database
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Models\Role\Validation\RoleValidator $roleValidator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleValidator $roleValidator)
    {
        // Retrieve posted data
        $data = Request::only('title');

        // Slugify role title
        $data['slug'] = Str::slug($data['title']);

        // Validate role and redirect if invalidate
        if (!$roleValidator->with($data)->validate()) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Role slug already exists');
        }

        try {
            $this->roleRepository->create($data);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully created.');
        } catch (Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not create role');
        }
    }

    /**
     * We only update title since slug is used for gates
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        // Retrieve posted data
        $data = Request::only('title');

        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (empty($role)) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Role does not exist');
        }

        // Check if an update is required
        if ($role->title == $data['title']) {
            return redirect()->route('nodes.backend.users.roles')->with('info', 'Role title has not changed. No update required');
        }

        try {
            $role->update($data);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully updated');
        } catch (Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not update role');
        }
    }

    /**
     * Delete the role
     * Note: This can cause quite the damage role is in use
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (!empty($role)) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Role does not exist');
        }

        // Make sure the role we're about to delete
        // is not the default role
        if ($role->isDefault()) {
            return redirect()->route('nodes.backend.users.roles')->with('warning', 'You can\'t delete the default role');
        }

        try {
            $this->roleRepository->deleteRole($role);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully deleted');
        } catch (Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not delete role');
        }
    }

    /**
     * Mark role as default
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault($id) {
        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (empty($role)) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Role does not exist');
        }

        // Make sure the role we're about to mark as default
        // isn't already the default role
        if ($role->isDefault()) {
            return redirect()->route('nodes.backend.users.roles')->with('warning', 'Role is already default');
        }

        try {
            $this->roleRepository->setDefault($role);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully set default');
        } catch (Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not set the role default');
        }

    }
}
