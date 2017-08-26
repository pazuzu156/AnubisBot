<?php

namespace Core\Base\Commands\User;

use Core\Base\Traits\Roleable;
use Core\Command\Command;
use Core\Command\Parameters;
use Discord\Parts\Guild\Role;

class Roles extends Command
{
    use Roleable;

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
     * @example {COMMAND} roles
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
     * @example {COMMAND} restrict <ROLE|LIST OF ROLES>
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
                $dataFile = $this->guild->dataFile()->getAsArray();

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

                $this->guild->dataFile()->write($dataFile);

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
     * @example {COMMAND} unrestrict <ROLE|LIST OF ROLES>
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
                $dataFile = $this->guild->dataFile()->getAsArray();

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

                $this->guild->dataFile()->write($dataFile);

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
     * @example {COMMAND} join <ROLE|LIST OF ROLES>
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
                    $member = $this->author;
                    $this->guild->members->save($member)->then(function ($user) use ($message, $msg) {
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
     * @example {COMMAND} leave <ROLE|LIST OF ROLES>
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
                    $member = $this->author;
                    $this->guild->members->save($member)->then(function ($user) use ($message, $msg) {
                        $message->reply($msg);
                    })->otherwise(function ($e) use ($message) {
                        $message->channel->sendMessage("```{$e->getMessage()}```");
                    });
                }
            }
        }
    }
}
