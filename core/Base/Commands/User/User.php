<?php

namespace Core\Base\Commands\User;

use Core\Command\Command;
use Core\Command\Parameters;

class User extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'user';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Manage the server\'s users';

    /**
     * Kicks a user.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function kick(Parameters $p)
    {
        if ($this->can('kick_members')) {
            if ($p->count()) {
                $id = $this->parseMemberId($p->first());
                $member = $this->guild->members->get('id', $id);
                $channel = $this->getBotSpam();
                
                // remove user id from params
                $params = $p->all();
                array_shift($params);

                if ($member) {
                    $this->guild->members->kick($member)->then(function ($member) use ($channel, $params) {
                        $msg = "Kicked user: $member";

                        if (count($params) > 0) {
                            $msg .= ' | Reason: '.implode(' ', $params);
                        }

                        $channel->sendMessage($msg);
                    });
                } else {
                    $this->message->reply('Sorry, but that user could not be found. Try again maybe?');
                }
            }
        }
    }

    /**
     * Bans a user.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function ban(Parameters $p)
    {
        if ($this->can('ban_members')) {
            if ($p->count()) {
                $id = $this->parseMemberId($p->first());
                $member = $this->guild->members->get('id', $id);
                $channel = $this->getBotSpam();
                
                // remove user id from params
                $params = $p->all();
                array_shift($params);

                if ($member) {
                    $member->ban(10)->then(function ($ban) use ($member, $channel, $params) {
                        $msg = "Banned user: $member";

                        if (count($params) > 0) {
                            $msg .= ' | Reason: '.implode(' ', $params);
                        }

                        $channel->sendMessage($msg);
                    });
                } else {
                    $this->message->reply('Sorry, but that user could not be found. Try again maybe?');
                }
            }
        }
    }
}
