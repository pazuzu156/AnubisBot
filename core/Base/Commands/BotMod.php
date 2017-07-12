<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Wrappers\File;

class BotMod extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'botmod';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Set bot automated stuff';

    /**
     * Sets the default bot-spam channel.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return mixed
     */
    public function setbotspam(Parameters $p)
    {
        dump(env('BOT_OWNER') == $this->author->id);
        if ($this->can('manage_server') || env('BOT_OWNER') == $this->author->id) {
            if ($p->count() > 0) {
                $channel = $this->guild->channels->get('name', $p->first());

                if (!is_null($channel)) {
                    $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                    if (isset($dataFile['bot_spam_channel'])) {
                        if ($dataFile['bot_spam_channel']['id'] == $channel->id) {
                            $this->message->reply('This channel is already set as the bot spam channel!');

                            return;
                        }
                    }

                    $dataFile['bot_spam_channel'] = [
                        'id'   => $channel->id,
                        'name' => $channel->name,
                    ];

                    File::writeAsJson($this->guild->dataFile(), $dataFile);
                    $this->message->reply('Bot spam channel changed to: #'.$channel->name.' (*'.$channel->id.'*)');
                }
            }
        } else {
            $this->message->reply('You do not have permission to run this command!');
        }
    }

    /**
     * Sets the message displayed when a user joines the server.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function joinmsg(Parameters $p)
    {
        if ($this->can('manage_server') || env('BOT_OWNER') == $this->author->id) {
            if ($p->count() > 0) {
                $message = rtrim(implode(' ', $p->all()), ' ');
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                if (!isset($dataFile['messages'])) {
                    $dataFile['messages'] = ['join' => '', 'leave' => ''];
                }

                $dataFile['messages']['join'] = $message;
                File::writeAsJson($this->guild->dataFile(), $dataFile);

                $this->message->reply('Welcome message now set!');
            }
        }
    }

    /**
     * Sets the message displayed when a user leaves the server.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function leavemsg(Parameters $p)
    {
        if ($this->can('manage_server') || env('BOT_OWNER') == $this->author->id) {
            if ($p->count() > 0) {
                $message = rtrim(implode(' ', $p->all()), ' ');
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                if (!isset($dataFile['messages'])) {
                    $dataFile['messages'] = ['join' => '', 'leave' => ''];
                }

                $dataFile['messages']['leave'] = $message;
                File::writeAsJson($this->guild->dataFile(), $dataFile);

                $this->message->reply('Dismissal message now set!');
            }
        }
    }

    /**
     * Resets the welcome message to the default.
     *
     * @return void
     */
    public function deljoinmsg()
    {
        if ($this->can('manage_server') || env('BOT_OWNER') == $this->author->id) {
            $dataFile = json_decode(File::get($this->guild->dataFile()), true);

            if (isset($dataFile['messages']['join'])) {
                $dataFile['messages']['join'] = '';
                File::writeAsJson($this->guild->dataFile(), $dataFile);
                $this->message->reply('Custom welcome message removed and reset to default!');
            }
        }
    }

    /**
     * Resets the dismissal message to the default.
     *
     * @return void
     */
    public function delleavemsg()
    {
        if ($this->can('manage_server') || env('BOT_OWNER') == $this->author->id) {
            $dataFile = json_decode(File::get($this->guild->dataFile()), true);

            if (isset($dataFile['messages']['leave'])) {
                $dataFile['messages']['leave'] = '';
                File::writeAsJson($this->guild->dataFile(), $dataFile);
                $this->message->reply('Custom dismissal message removed and reset to default!');
            }
        }
    }
}
