<?php

namespace Nodes\Backend\Models\Role;

use Nodes\Database\Eloquent\Repository as NodesRepository;
use Nodes\Exceptions\Exception;

/**
 * Class RoleRepository.
 */
class RoleRepository extends NodesRepository
{
    /**
     * Constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param \Nodes\Backend\Models\Role\Role $model
     */
    public function __construct(Role $model)
    {
        $this->setupRepository($model);
    }

    /**
     * Retrieve list of available roles.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return array
     */
    public function getList()
    {
        return $this->lists('title', 'slug');
    }

    /**
     * Retrieve roles for authed user's user-role.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return array
     */
    public function getListUserLevel()
    {
        // Retrieve full list
        $list = $this->getList();

        // If user is developer, give the full list
        if (\Gate::allows('backend-developer')) {
            return $list;
        }

        // This means user is not developer, let's unset that option
        unset($list['developer']);
        if (\Gate::allows('backend-super-admin')) {
            return $list;
        }

        // This means user is not super-admin, let's unset that option
        unset($list['super-admin']);


        // If user is admin, we return the list
        if (\Gate::allows('backend-admin')) {
            return $list;
        }

        // If user is not even admin, that option should not be possible either
        unset($list['admin']);

        return $list;
    }

    /**
     * Retrieve all users paginated.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  int $limit
     * @param  array   $fields
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedForBackend($limit = 25, $fields = ['*'])
    {
        return $this->paginate($limit, $fields);
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Nodes\Backend\Models\Role\Role
     * @throws \Nodes\Database\Exceptions\EntityNotFoundException
     */
    public function getDefaultRole()
    {
        return $this->getByOrFail('default', true);
    }

    /**
     * Delete role and clean up after.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  \Nodes\Backend\Models\Role\Role $role
     * @return bool
     * @throws \Nodes\Exceptions\Exception
     */
    public function deleteRole(Role $role)
    {
        $defaultRole = $this->getDefaultRole();

        // Check if the role which is about to be deleted is the default role
        if ($defaultRole->id == $role->id) {
            throw new Exception('Cannot delete default role', 500);
        }

        // Set all user's with deleted role
        // to the default role 'user'.
        if (! empty($defaultRole)) {
            $role->users()->update(['user_role' => $defaultRole->slug]);
        }

        return (bool) $role->delete();
    }

    /**
     * Set role as default.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param \Nodes\Backend\Models\Role\Role $role
     * @return \Exception
     */
    public function setDefault(Role $role)
    {
        try {
            // Begin transaction
            $this->beginTransaction();

            // Look up already default role and set it to non default
            $defaultRole = $this->getBy('default', true);
            if ($defaultRole) {
                $defaultRole->update(['default' => false]);
            }

            // Update current role to default
            $role->update(['default' => true]);

            // Commit transaction
            $this->commitTransaction();
        } catch (\Exception $e) {

            // Rollback and throw
            $this->rollbackTransaction();

            return $e;
        }
    }
}
