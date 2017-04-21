<?php

namespace Core\Base\Commands\User;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Wrappers\File;
use Discord\Parts\Guild\Role;

class Roles extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'roles';

    /**
     * {@inheritdoc}
     */
    protected $description = 'List server\'s joinable roles';

    /**
     * Default index method.
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $msg = "List of joinable roles:\n```";

        foreach ($this->getJoinableRoles() as $role) {
            $msg .= strtolower($role->name)."\n";
        }

        $this->channel->sendMessage("$msg```");
    }

    /**
     * Adds a role to the server's restriction list.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function restrict(Parameters $p)
    {
        if ($this->can('manage_roles') && $p->count() > 0) {
            $roleToRestrict = $this->getRoleFromParameter($p);

            if ($roleToRestrict !== false && !$this->isRoleRestricted($roleToRestrict)) {
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);
                $dataFile['restricted_roles'][] = [
                    'id' => $roleToRestrict->id,
                    'name' => strtolower($roleToRestrict->name),
                ];

                File::writeAsJson($this->guild->dataFile(), $dataFile);

                $this->message->reply('Role "'.$roleToRestrict->name.'" is now restricted!');
            } else {
                $this->message->reply("Either that role doesn't exist or it's already restricted!");
            }
        }
    }

    /**
     * Removes a role to the server's restriction list.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function unrestrict(Parameters $p)
    {
        if ($this->can('manage_roles') && $p->count() > 0) {
            $roleToRemove = $this->getRoleFromParameter($p);

            if ($roleToRemove !== false && $this->isRoleRestricted($roleToRemove)) {
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                $keyToRemove = 0;
                $restrictedRoles = $dataFile['restricted_roles'];
                for ($i = 0; $i < count($restrictedRoles); $i++) {
                    if ($roleToRemove->id == $restrictedRoles[$i]['id']) {
                        $keyToRemove = $i;
                        break;
                    }
                }

                $name = $restrictedRoles[$keyToRemove]['name'];
                unset($dataFile['restricted_roles'][$keyToRemove]);

                File::writeAsJson($this->guild->dataFile(), $dataFile);

                $this->message->reply('Role "'.$roleToRemove->name.'" is no longer restricted!');
            } else {
                $this->message->reply("Either that role doesn't exist or isn't restricted!");
            }
        }
    }

    /**
     * Join a role.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function join(Parameters $p)
    {
        if ($p->count() > 0) {
            $roleToJoin = $this->getRoleFromParameter($p);

            if ($roleToJoin !== false && !$this->isRoleRestricted($roleToJoin)) {
                if (!$this->hasRole($roleToJoin)) {
                    $this->author->addRole($roleToJoin);

                    $message = $this->message;
                    $this->guild->members->save($this->author)->then(function ($user) use ($message, $roleToJoin) {
                        $message->reply('You have joined the role: '.$roleToJoin->name);
                    })->otherwise(function ($e) use ($message) {
                        $message->channel->sendMessage("```{$e->getMessage()}```");
                    });
                } else {
                    $this->message->reply('You already have that role!');
                }
            }
        }
    }

    /**
     * Leave a role.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function leave(Parameters $p)
    {
        if ($p->count() > 0) {
            $roleToLeave = $this->getRoleFromParameter($p);

            if ($roleToLeave !== false) {
                if ($this->hasRole($roleToLeave)) {
                    $this->author->removeRole($roleToLeave);

                    $message = $this->message;
                    $this->guild->members->save($this->author)->then(function ($user) use ($message, $roleToLeave) {
                        $message->reply('You have left the role: '.$roleToLeave->name);
                    })->otherwise(function ($e) use ($message) {
                        $message->channel->sendMessage("```{$e->getMessage()}```");
                    });
                } else {
                    $this->message->reply('You do not have that role!');
                }
            }
        }
    }

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
        if (File::exists($this->guild->dataFile())) {
            return json_decode(File::get($this->guild->dataFile()))->restricted_roles;
        }

        File::writeAsJson($this->guildDataFile(), [
            'restricted_roles' => [],
        ]);

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
        $role = null;

        foreach ($this->guild->roles as $guildRole) {
            if (strtolower($p->first()) == strtolower($guildRole->name)) {
                $role = $guildRole;
            }
        }

        return (!is_null($role)) ? $role : false;
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
}
