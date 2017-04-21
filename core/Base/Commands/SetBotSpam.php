<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Wrappers\File;

class SetBotSpam extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'setbotspam';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Set the bot spam channel (Where the bot spits out automated messages)';

    /**
     * Default command method.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return mixed
     */
    public function index(Parameters $p)
    {
        if ($p->count() > 0) {
            $channel = $this->guild->channels->get('name', $p->first());

            if (!is_null($channel)) {
                $dataFile = json_decode(File::get($this->guild->dataFile()), true);

                if (isset($dataFile['bot_spam_channel'])) {
                    if ($dataFile['bot_spam_channel']['id'] == $channel->id) {
                        $this->message->reply('This channel is already set as the bot spam channel!');
                        return;
                    }

                    $dataFile['bot_spam_channel'] = [
                        'id'   => $channel->id,
                        'name' => $channel->name,
                    ];
                }

                // this command needs a really high priority.
                // (admin or any who can manage server)
                if ($this->can('manage_server')) {
                    File::writeAsJson($this->guild->dataFile(), $dataFile);
                    $this->message->reply('Bot spam channel changed to: #'.$channel->name.' (*'.$channel->id.'*)');
                } else {
                    $this->message->reply('You do not have permission to run this command!');
                }
            }
        }
    }
}
