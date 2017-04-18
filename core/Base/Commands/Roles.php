<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Wrapper\FileSystemWrapper as File;

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

    public function index()
    {
        $msg = "List of joinable roles:\n```";
        foreach ($this->guild->roles as $role) {
            $msg .= strtolower($role->name)."\n";
        }
        $msg .= '```';

        $this->channel->sendMessage($msg);
    }

    public function restrict(Parameters $p)
    {
        $file = data_path().'/'.$this->getGuildId();

        $data = File::get($file);
        dump($data);
    }

    private function getJoinableRoles()
    {
        $guildid = md5($this->guild->id);
        $roles = $this->guild->roles;

        $file = data_path().'/'.$guildid;

        if (file_exists($file)) {
            // TODO: Open
        }
    }

    private function getGuildId()
    {
        return md5($this->guild->id);
    }
}
