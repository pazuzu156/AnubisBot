<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Wrappers\FileSystemWrapper as File;

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
    public function index()
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
        if ($this->can('manage_roles')) {
            $roles = $this->getRestrictedRoles();

            if ($p->count() > 0) {
                $roleToRestrict = $this->getRoleFromParameter($p);

                if ($roleToRestrict !== false) {
                    if (!$this->isRoleRestricted($roleToRestrict, $roles)) {
                        $data = [
                            'id' => $roleToRestrict->id,
                            'name' => strtolower($roleToRestrict->name),
                        ];

                        $dataFile = json_decode(File::get($this->guildDataFile()), true);
                        $dataFile['restricted_roles'][] = $data;

                        File::writeAsJson($this->guildDataFile(), $dataFile);

                        $this->message->reply('Role "'.$p->first().'" is now restricted');
                    } else {
                        $this->message->reply('The role "'.$p->first().'" is already restricted!');
                    }
                } else {
                    $this->message->reply('The role "'.$p->first().'" is not a role listed in this server!');
                }
            } else {
                $this->message->reply('You forgot to supply the role you want to restrict!');
            }
        } else {
            $this->message->reply('You do not have access to that command!');
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
        if ($this->can('manage_roles')) {
            $roles = $this->getRestrictedRoles();

            if ($p->count() > 0) {
                $roleToRemove = $this->getRoleFromParameter($p);

                if ($roleToRemove !== false) {
                    if ($this->isRoleRestricted($roleToRemove, $roles)) {
                        $dataFile = json_decode(File::get($this->guildDataFile()), true);

                        $keyToRemove = 0;
                        foreach ($dataFile['restricted_roles'] as $key => $value) {
                            $r = $dataFile['restricted_roles'][$key];

                            if ($roleToRemove->id == $r['id']) {
                                $keyToRemove = $key;
                                break;
                            }
                        }

                        $name = $dataFile['restricted_roles'][$keyToRemove]['name'];
                        unset($dataFile['restricted_roles'][$keyToRemove]);

                        File::writeAsJson($this->guildDataFile(), $dataFile);

                        $this->message->reply('The role "'.$p->first().'" is no longer restricted');
                    } else {
                        $this->message->reply('The role "'.$p->first().'" is not restricted!');
                    }
                } else {
                    $this->message->reply('The role "'.$p->first().'" is not a role listed in this server!');
                }
            } else {
                $this->message->reply('You forgot to supply the role you want to restrict!');
            }
        } else {
            $this->message->reply('You do not have access to that command!');
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
            $roles = $this->getRestrictedRoles();

            if ($roleToJoin !== false) {
                if (!$this->isRoleRestricted($roleToJoin, $roles)) {
                    if (!$this->hasRole($roleToJoin)) {
                        $this->author->addRole($roleToJoin);

                        $channel = $this->channel;
                        $message = $this->message;
                        $this->guild->members->save($this->author)->then(function ($user) use ($message, $roleToJoin) {
                            $message->reply('You have joined the role: '.$roleToJoin->name);
                        })->otherwise(function ($e) use ($channel) {
                            $channel->sendMessage("```{$e->getMessage()}```");
                        });
                    } else {
                        $this->message->reply('You already have that role!');
                    }
                } else {
                    $this->message->reply('"'.$p->first().'" is not a joinable role!');
                }
            } else {
                $this->message->reply('That role does not exist. Run `'.env('PREFIX', '').'roles` to get a list of joinable roles');
            }
        } else {
            $this->message->reply('You forgot to supply the role you want to join!');
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
            $roles = $this->author->roles;

            dump($this->hasRole($roleToLeave));

            if ($roleToLeave !== false) {
                if ($this->hasRole($roleToLeave)) {
                    $this->author->removeRole($roleToLeave);

                    $channel = $this->channel;
                    $message = $this->message;
                    $this->guild->members->save($this->author)->then(function ($user) use ($message, $roleToLeave) {
                        $message->reply('You have left the role: '.$roleToLeave->name);
                    })->otherwise(function ($e) use ($channel) {
                        $channel->sendMessage("```{$e->getMessage()}```");
                    });
                } else {
                    $this->message->reply('You do not have that role!');
                }
            } else {
                $this->message->reply('That role does not exist. Run `'.env('PREFIX', '').'roles` to get a list of joinable roles');
            }
        } else {
            $this->message->reply('You forgot to supply the role you want to leave!');
        }
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

        foreach ($this->guild->roles as $r) {
            if (strtolower($p->first()) == strtolower($r->name)) {
                $role = $r;
                return $role;
            }
        }

        return (!is_null($role)) ? $role : false;
    }

    /**
     * Gets the requested role from the Guild object.
     *
     * @return mixed
     */
    private function getRestrictedRoles()
    {
        if (File::exists($this->guildDataFile())) {
            return json_decode(File::get($this->guildDataFile()))->restricted_roles;
        }

        File::writeAsJson($this->guildDataFile(), [
            'restricted_roles' => [],
        ]);

        return $this->getRestrictedRoles();
    }

    /**
     * Checks wheter a role is restricted or not.
     *
     * @param \Discord\Parts\Guild\Role
     * @param mixed $json
     *
     * @return mixed
     */
    private function isRoleRestricted($role, $json)
    {
        foreach ($json as $item) {
            $arr = (array)$item;

            if (strtolower($role->name) == strtolower($arr['name'])) {
                return true;
            }
        }

        return false;
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
        $roles = $this->guild->roles;
        $restrictedRoles = $this->getRestrictedRoles();

        foreach ($roles as $role) {
            if (!in_array(strtolower($role->name), $filters)) {
                $restricted = false;

                foreach ($restrictedRoles as $rs) {
                    if ($role->id == $rs->id) {
                        $restricted = true;
                    }
                }

                if (!$restricted) {
                    $joinableRoles[] = $role;
                }
            }
        }

        return $joinableRoles;
    }

    /**
     * Checks whether a user has the requested role or not.
     *
     * @param \Discord\Parts\User\Role $role
     *
     * @return bool
     */
    private function hasRole($role)
    {
        foreach ($this->author->roles as $r) {
            if ($role->id == $r->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the guild id in md5 hashed form.
     *
     * @return string
     */
    private function getHashedGuildId()
    {
        return md5($this->guild->id);
    }

    /**
     * Gets the guild's data file location.
     *
     * @return string
     */
    private function guildDataFile()
    {
        return data_path().'/'.$this->getHashedGuildId();
    }
}
