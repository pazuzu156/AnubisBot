<?php

namespace Core;

use Core\Config\Configuration;
use Discord\Parts\User\Member;

class Permissions
{
    /**
     * Checks to see if a user can do something based
     * on the permission.
     *
     * @param string                     $permission
     * @param \Discord\Parts\User\Member $member
     *
     * @return boolean
     */
    public function can($permission, Member $member)
    {
        foreach($member->roles as $role) {
            return $role->permissions->$permission;
        }

        return false;
    }
}
