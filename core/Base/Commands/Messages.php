<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;

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

    /**
     * Default message history limit.
     *
     * @var int
     */
    private $_defaultLimit = 50;

    /**
     * Prune messages.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function prune(Parameters $p)
    {
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
                        $channel->messages->delete($msg);
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

    /**
     * Prune user messages.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function pruneuser(Parameters $p)
    {
        $limit = (!is_null($p->get(1))) ? (int) $p->get(1) : $this->_defaultLimit;

        if ($this->can('manage_messages')) {
            $user = $this->member($p->first());

            if ($user) {
                $channel = $this->channel;
                $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel, $user) {
                    $count = 0;

                    foreach ($msgs as $msg) {
                        $s = "ID: {$user->id} | ID2: {$msg->author->id}";
                        if ($user->id == $msg->author->id) {
                            if ($count % 5) {
                                $channel->messages->delete($msg);
                            } else {
                                tsleep(1.5);
                                $channel->messages->delete($msg);
                            }

                            $count++;
                        }
                    }
                });
            } else {
                $this->message->reply('You either forgot to give the user, or the user does not exist!');
            }
        } else {
            $this->message->reply('You do not have permission to prune messages!');
        }
    }

    /**
     * Prune bot messages.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
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
                            $channel->messages->delete($msg);
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
