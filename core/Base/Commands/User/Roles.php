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
        $msg = "List of joinable roles:```\n";

        foreach ($this->getJoinableRoles() as $role) {
            $msg .= strtolower($role->name)."\n";
        }

        $this->channel->sendMessage("$msg```");
    }

    /**
     * Adds a role, or roles to the server's restriction list.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function restrict(Parameters $p)
    {
        if ($this->can('manage_roles') && $p->count() > 0) {
            $roleToRestrict = $this->getRoleFromParameter($p);

            if ($roleToRestrict !== false) {
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                if (is_array($roleToRestrict)) {
                    foreach ($roleToRestrict as $role) {
                        if (!$this->isRoleRestricted($role)) {
                            $dataFile['restricted_roles'][] = [
                                'id'   => $role->id,
                                'name' => strtolower($role->name),
                            ];
                        } else {
                            $this->message->reply('That role is already restricted!');
                        }
                    }
                } else {
                    if (!$this->isRoleRestricted($roleToRestrict)) {
                        $dataFile['restricted_roles'][] = [
                            'id'   => $roleToRestrict->id,
                            'name' => strtolower($roleToRestrict->name),
                        ];
                    } else {
                        $this->message->reply('That role is already restricted!');
                    }
                }

                $dataFile['restricted_roles'] = array_values($dataFile['restricted_roles']);

                File::writeAsJson($this->guild->dataFile(), $dataFile);

                if ($p->count() == 1) {
                    $this->message->reply('Role "'.$roleToRestrict->name.'" is now restricted!');
                } else {
                    $msg = 'Roles: ';
                    foreach ($roleToRestrict as $role) {
                        $msg .= "\"{$role->name}\", ";
                    }
                    $msg = rtrim($msg, ', ');
                    $this->message->reply($msg.' are now restricted!');
                }
            } else {
                $this->message->reply('That role doesn\'t exist!');
            }
        }
    }

    /**
     * Removes a role, or roles from the server's restriction list.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function unrestrict(Parameters $p)
    {
        if ($this->can('manage_roles') && $p->count() > 0) {
            $roleToRemove = $this->getRoleFromParameter($p);

            if ($roleToRemove !== false) {
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                $restrictedRoles = $dataFile['restricted_roles'];

                if (is_array($roleToRemove)) {
                    foreach ($roleToRemove as $role) {
                        if ($this->isRoleRestricted($role)) {
                            $keyToRemove = 0;
                            for ($i = 0; $i < count($restrictedRoles); $i++) {
                                if ($role->id == $restrictedRoles[$i]['id']) {
                                    $keyToRemove = $i;
                                    break;
                                }
                            }

                            $name = $restrictedRoles[$keyToRemove]['name'];
                            unset($dataFile['restricted_roles'][$keyToRemove]);
                        }
                    }
                } else {
                    if ($this->isRoleRestricted($roleToRemove)) {
                        $keyToRemove = 0;
                        for ($i = 0; $i < count($restrictedRoles); $i++) {
                            if ($roleToRemove->id == $restrictedRoles[$i]['id']) {
                                $keyToRemove = $i;
                                break;
                            }
                        }

                        $name = $restrictedRoles[$keyToRemove]['name'];
                        unset($dataFile['restricted_roles'][$keyToRemove]);
                    }
                }

                $dataFile['restricted_roles'] = array_values($dataFile['restricted_roles']);

                File::writeAsJson($this->guild->dataFile(), $dataFile);

                if ($p->count() == 1) {
                    $this->message->reply('Role "'.$roleToRemove->name.'" is no longer restricted!');
                } else {
                    $msg = 'Roles: ';
                    foreach ($roleToRemove as $role) {
                        $msg .= "\"{$role->name}\", ";
                    }
                    $msg = rtrim($msg, ', ');
                    $this->message->reply($msg.' are no longer restricted!');
                }
            } else {
                $this->message->reply('That role doesn\'t exist!');
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

            if ($roleToJoin !== false) {
                $msg = 'You have joined the role';
                $goahead = true;

                if (is_array($roleToJoin)) {
                    $roles = '';

                    foreach ($roleToJoin as $role) {
                        if (!$this->isRoleRestricted($role)) {
                            if (!$this->hasRole($role)) {
                                $this->author->addRole($role);
                                $roles .= $role->name.', ';
                            } else {
                                $goahead = false;
                                $this->message->reply('You are already in the role: '.$role->name);
                            }
                        }
                    }

                    if ($roles !== '') {
                        $msg .= 's: '.rtrim($roles, ', ');
                    }
                } else {
                    if (!$this->isRoleRestricted($roleToJoin)) {
                        if (!$this->hasRole($roleToJoin)) {
                            $this->author->addRole($roleToJoin);
                            $msg .= ': '.$roleToJoin->name;
                        } else {
                            $goahead = false;
                            $this->message->reply('You already have that role!');
                        }
                    }
                }

                if ($goahead) {
                    $message = $this->message;
                    $this->guild->members->save($this->author)->then(function ($user) use ($message, $msg) {
                        $message->reply($msg);
                    })->otherwise(function ($e) use ($message) {
                        $message->channel->sendMessage("```{$e->getMessage()}```");
                    });
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
                $msg = 'You have left the role';
                $goahead = true;

                if (is_array($roleToLeave)) {
                    $roles = '';

                    foreach ($roleToLeave as $role) {
                        if (!$this->isRoleRestricted($role)) {
                            if ($this->hasRole($role)) {
                                $this->author->removeRole($role);
                                $roles .= $role->name.', ';
                            } else {
                                $goahead = false;
                                $this->message->reply('You do not have the role: '.$role->name);
                            }
                        }
                    }

                    if ($roles !== '') {
                        $msg .= 's: '.rtrim($roles, ', ');
                    }
                } else {
                    if (!$this->isRoleRestricted($roleToLeave)) {
                        if ($this->hasRole($roleToLeave)) {
                            $this->author->removeRole($roleToLeave);
                            $msg .= ': '.$roleToLeave->name;
                        } else {
                            $goahead = false;
                            $this->message->reply('You do not have that role!');
                        }
                    }
                }

                if ($goahead) {
                    $message = $this->message;
                    $this->guild->members->save($this->author)->then(function ($user) use ($message, $msg) {
                        $message->reply($msg);
                    })->otherwise(function ($e) use ($message) {
                        $message->channel->sendMessage("```{$e->getMessage()}```");
                    });
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

        File::writeAsJson($this->guild->dataFile(), [
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
}
