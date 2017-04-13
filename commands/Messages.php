<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

class Messages extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'msg';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Moderates user messages';

    private $_defaultLimit = 50;

    public function prune(Parameters $p)
    {
        // $this->alias('prune', 'index', $p);
        $limit = ($p->count()) ? (int) $p->first() : $this->_defaultLimit;

        if ($this->can('manage_messages')) {
            $channel = $this->channel;
            $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel) {
                $count = 0;
                foreach ($msgs as $msg) {
                    $count++;

                    if ($count % 5) {
                        $channel->messages->delete($msg);
                    } else {
                        tsleep(1.5);
                    }
                }

                if ($count == 1) {
                    $channel->sendMessage('`Pruned 1 message`');
                } else {
                    $channel->sendMessage("`Pruned $count messages`");
                }
            });
        } else {
            $this->message->reply('You do not have permission to run this command!');
        }
    }

    public function pruneuser(Parameters $p)
    {
        $limit = (!is_null($p->get(1))) ? (int) $p->get(1) : $this->_defaultLimit;

        if ($this->can('manage_messages')) {
            $userid = str_replace('<@', '', rtrim($p->get(0), '>'));
            $user = $this->guild->members[$userid];

            $channel = $this->channel;
            $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel, $user) {
                $count = 0;
                foreach ($msgs as $msg) {
                    if ($user->id == $msg->author->id) {
                        $count++;

                        if ($count % 5) {
                            $channel->messages->delete($msg);
                        } else {
                            tsleep(1.5);
                        }
                    }
                }

                if ($count == 1) {
                    $channel->sendMessage('`Pruned 1 message`');
                } else {
                    $channel->sendMessage("`Pruned $count messages`");
                }
            });
        } else {
            $this->message->reply('You do not have permission to prune messages!');
        }
    }

    public function prunebot(Parameters $p)
    {
        $limit = ($p->count()) ? (int) $p->first() : $this->_defaultLimit;

        $channel = $this->channel;
        $bot = $this->app->getBotUser();

        if ($this->can('manage_messages')) {
            $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel, $bot) {
                $count = 0;
                foreach ($msgs as $msg) {
                    if ($bot->id == $msg->author->id) {
                        $count++;

                        if ($count % 5) {
                            $channel->messages->delete($msg);
                        } else {
                            tsleep(1.5);
                        }
                    }
                }

                if ($count == 1) {
                    $channel->sendMessage('`Pruned 1 message`');
                } else {
                    $channel->sendMessage("`Pruned $count messages`");
                }
            });
        }
    }
}
