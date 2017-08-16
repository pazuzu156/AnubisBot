<?php

namespace Core\Base\Traits;

use Core\Command\Parameters;
use Discord\Parts\Guild\Role;
use Discord\Parts\User\Member;

trait Roleable
{
    /**
     * Returns a list of joinable roles.
     *
     * @return array
     */
    private function getJoinableRoles()
    {
        $joinableRoles = [];
        $filters = ['@everyone'];

        foreach ($this->guild->roles as $guildRole) {
            if (!in_array(strtolower($guildRole->name), $filters)) {
                $restricted = false;

                foreach ($this->getRestrictedRoles() as $restrictedRole) {
                    if ($guildRole->id == $restrictedRole->id) {
                        $restricted = true;
                    }
                }

                if (!$restricted) {
                    $joinableRoles[] = $guildRole;
                }
            }
        }

        return $joinableRoles;
    }

    /**
     * Gets the requested role from the Guild object.
     *
     * @return mixed
     */
    private function getRestrictedRoles()
    {
        $dataFile = $this->guild->dataFile();

        if ($dataFile->exists()) {
            return $dataFile->getAsObject()->restricted_roles;
        }

        $dataFile->write(['restricted_roles' => []]);

        return $this->getRestrictedRoles();
    }

    /**
     * Gets the requested role from the Guild object.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return mixed
     */
    private function getRoleFromParameter(Parameters $p)
    {
        $roles = null;

        if ($p->count() > 1) {
            foreach ($this->guild->roles as $guildRole) {
                for ($i = 0; $i < $p->count(); $i++) {
                    if (strtolower($p->get($i)) == strtolower($guildRole->name)) {
                        $roles[] = $guildRole;
                    }
                }
            }
        } else {
            foreach ($this->guild->roles as $guildRole) {
                if (strtolower($p->first()) == strtolower($guildRole->name)) {
                    $roles = $guildRole;
                }
            }
        }

        return (!is_null($roles)) ? $roles : false;
    }

    /**
     * Checks wheter a role is restricted or not.
     *
     * @param \Discord\Parts\Guild\Role
     *
     * @return mixed
     */
    private function isRoleRestricted(Role $role)
    {
        foreach ($this->getRestrictedRoles() as $restrictedRole) {
            $arr = (array) $restrictedRole;

            if (strtolower($role->name) == strtolower($arr['name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether a user has the requested role or not.
     *
     * @param \Discord\Parts\User\Role $role
     *
     * @return bool
     */
    private function hasRole(Role $role)
    {
        foreach ($this->author->roles as $userRole) {
            if ($role->id == $userRole->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the roles of the given member, or the message author.
     *
     * @param \Discord\Parts\User\Member $member
     *
     * @return \Discord\Repository\Guild\RoleRepository
     */
    private function getUserRoles(Member $member = null)
    {
        return (is_null($member)) ? $this->author->roles : $member->roles;
    }
}
