<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

use Discord\Parts\User\Member;

class Prune extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'prune';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Prune messages for a user, bot or all messages. Default limit: 50';

    /**
     * Prune all messages limited by limit
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $limit = (!is_null($p->first())) ? (int)$p->first() : 50;

        $channel = $this->channel;
        $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel) {
            $arr = [];
            foreach ($msgs as $message) {
                $arr[] = $message;
            }

            $channel->deleteMessages($arr);

            if (count($arr) == 1) {
                $channel->sendMessage('`pruned 1 message`');
            } else {
                $channel->sendMessage('`pruned '.count($arr).' messages`');
            }
        });
    }

    /**
     * Prune all user messages limited by limit
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function user(Parameters $p)
    {
        $limit = (!is_null($p->get(1))) ? (int)$p->get(1) : 50;

        if ($this->permissions->can('manage_messages', $this->author)) {
            $userid = str_replace('<@', '', rtrim($p->get(0), '>'));
            $user = $this->guild->members[$userid];

            $channel = $this->channel;
            $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel, $user) {
                $arr = [];
                foreach ($msgs as $message) {
                    if ($user->id == $message->author->id) {
                        $arr[] = $message;
                    }
                }
                
                $channel->deleteMessages($arr);

                if (count($arr) == 1) {
                    $channel->sendMessage('`pruned 1 message`');
                } else {
                    $channel->sendMessage('`pruned '.count($arr).' messages`');
                }
            });
        } else {
            $this->message->reply("You do not have permission to prune messages!");
        }
    }

    /**
     * Prune all bot messages limited by limit
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function bot(Parameters $p)
    {
        $limit = (!is_null($p->first())) ? (int)$p->first() : 50;

        $channel = $this->channel;
        $bot = $this->app->getBotUser();

        $channel->getMessageHistory(['limit' => $limit])->then(function ($msgs) use ($channel, $bot) {
            $arr = [];
            foreach ($msgs as $message) {
                if ($bot->id == $message->author->id) {
                    $arr[] = $message;
                }
            }

            $channel->deleteMessages($arr);

            if (count($arr) == 1) {
                $channel->sendMessage('`pruned 1 message`');
            } else {
                $channel->sendMessage('`pruned '.count($arr).' messages`');
            }
        });
    }
}
