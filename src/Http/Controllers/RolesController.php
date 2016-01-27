<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
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
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param  \Nodes\Backend\Models\Role\RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        if (\Gate::denies('backend-developer')) {
            abort(403);
        }

        $this->roleRepository = $roleRepository;
    }

    /**
     * List all roles
     *
     * @author Morten Rugaard <moru@nodes.dk>
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
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param \Nodes\Backend\Models\Role\Validation\RoleValidator $roleValidator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleValidator $roleValidator)
    {
        // Retrieve posted data
        $data = \Input::only('title');

        // Slugify role title
        $data['slug'] = Str::slug($data['title']);

        // Validate role and redirect if invalidate
        if (!$roleValidator->with($data)->validate()) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Role slug already exists');
        }

        // Try to save role and redirect with success or error
        try {
            $this->roleRepository->create($data);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully created.');
        } catch (\Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not create role');
        }
    }

    /**
     * We only update title since slug is used for gates
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        // Retrieve posted data
        $data = \Input::only('title');

        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'The role was not found');
        }

        // Check if an update is required
        if ($role->title == $data['title']) {
            return redirect()->route('nodes.backend.users.roles')->with('info', 'Role title has not changed. No update required');
        }

        try {
            $role->update($data);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully updated');
        } catch (\Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not update role');
        }
    }

    /**
     * Delete the roll
     * Warning this can do a lot of damage if the role is used
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'The role was not found');
        }

        if ($role->isDefault()) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'The role you wish to delete is the default one');
        }

        try {
            $this->roleRepository->deleteRole($role);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully deleted');
        } catch (\Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not delete role');
        }
    }

    /**
     * Update a role to default and find the existing default role and set that to non default
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault($id) {
        // Retrieve role by ID
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'The role was not found');
        }

        // Fail if role is already default
        if ($role->isDefault()) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'The role is already default');
        }

        // Update roles and redirect
        try {
            $this->roleRepository->setDefault($role);
            return redirect()->route('nodes.backend.users.roles')->with('success', 'Role was successfully set default');
        } catch (\Exception $e) {
            return redirect()->route('nodes.backend.users.roles')->with('error', 'Could not set the role default');
        }

    }
}
