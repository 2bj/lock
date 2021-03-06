<?php
namespace BeatSwitch\Lock\Drivers;

use BeatSwitch\Lock\Contracts\Caller;
use BeatSwitch\Lock\Contracts\Driver;
use BeatSwitch\Lock\Contracts\Permission;
use BeatSwitch\Lock\Contracts\Role;

/**
 * A static in-memory driver
 */
class ArrayDriver implements Driver
{
    /**
     * A list of active permissions
     *
     * @var array
     */
    protected $permissions = array();

    /**
     * Returns all the permissions for a caller
     *
     * @param \BeatSwitch\Lock\Contracts\Caller $caller
     * @return \BeatSwitch\Lock\Contracts\Permission[]
     */
    public function getPermissions(Caller $caller)
    {
        $key = $this->getCallerKey($caller);

        return array_key_exists($key, $this->permissions) ? $this->permissions[$key] : [];
    }

    /**
     * Stores a new permission into the driver for a caller
     *
     * @param \BeatSwitch\Lock\Contracts\Caller $caller
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return void
     */
    public function storePermission(Caller $caller, Permission $permission)
    {
        $this->permissions[$this->getCallerKey($caller)][] = $permission;
    }

    /**
     * Removes a permission from the driver for a caller
     *
     * @param \BeatSwitch\Lock\Contracts\Caller $caller
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return void
     */
    public function removePermission(Caller $caller, Permission $permission)
    {
        // Remove permissions which match the action and resource
        $this->permissions[$this->getCallerKey($caller)] = array_filter(
            $this->getPermissions($caller),
            function (Permission $callerPermission) use ($permission) {
                // Only keep permissions which don't exactly match the one which we're trying to remove.
                return ! $callerPermission->matchesPermission($permission);
            }
        );
    }

    /**
     * Checks if a permission is stored for a user
     *
     * @param \BeatSwitch\Lock\Contracts\Caller $caller
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return bool
     */
    public function hasPermission(Caller $caller, Permission $permission)
    {
        // Iterate over each permission from the user and check if the permission is in the array.
        foreach ($this->getPermissions($caller) as $callerPermission) {
            // If a matching permission was found, immediately break the sequence and return true.
            if ($callerPermission->matchesPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all the permissions for a role
     *
     * @param \BeatSwitch\Lock\Contracts\Role $role
     * @return \BeatSwitch\Lock\Contracts\Permission[]
     */
    public function getRolePermissions(Role $role)
    {
        $key = $this->getRoleKey($role);

        return array_key_exists($key, $this->permissions) ? $this->permissions[$key] : [];
    }

    /**
     * Stores a new permission for a role
     *
     * @param \BeatSwitch\Lock\Contracts\Role $role
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return void
     */
    public function storeRolePermission(Role $role, Permission $permission)
    {
        $this->permissions[$this->getRoleKey($role)][] = $permission;
    }

    /**
     * Removes a permission for a role
     *
     * @param \BeatSwitch\Lock\Contracts\Role $role
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return void
     */
    public function removeRolePermission(Role $role, Permission $permission)
    {
        // Remove permissions which match the action and resource
        $this->permissions[$this->getRoleKey($role)] = array_filter(
            $this->getRolePermissions($role),
            function (Permission $rolePermission) use ($permission) {
                // Only keep permissions which don't exactly match the one which we're trying to remove.
                return ! $rolePermission->matchesPermission($permission);
            }
        );
    }

    /**
     * Checks if a permission is stored for a role
     *
     * @param \BeatSwitch\Lock\Contracts\Role $role
     * @param \BeatSwitch\Lock\Contracts\Permission
     * @return bool
     */
    public function hasRolePermission(Role $role, Permission $permission)
    {
        // Iterate over each permission from the user and check if the permission is in the array.
        foreach ($this->getRolePermissions($role) as $rolePermission) {
            // If a matching permission was found, immediately break the sequence and return true.
            if ($rolePermission->matchesPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Creates a key to store the caller's permissions
     *
     * @param \BeatSwitch\Lock\Contracts\Caller $caller
     * @return string
     */
    private function getCallerKey(Caller $caller)
    {
        return 'caller_' . $caller->getCallerType() . '_' . $caller->getCallerId();
    }

    /**
     * Creates a key to store the role's permissions
     *
     * @param \BeatSwitch\Lock\Contracts\Role $role
     * @return string
     */
    private function getRoleKey(Role $role)
    {
        return 'role_' . $role->getRoleName();
    }
}
