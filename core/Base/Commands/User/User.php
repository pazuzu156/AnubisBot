<?php

namespace Core\Base\Commands\User;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Utils\Color;
use Discord\Parts\Embed\Embed;

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
                $member = $this->member($p->first());
                $channel = $this->getBotSpam();

                // remove user id from params
                $params = $p->all();
                array_shift($params);

                if ($member) {
                    $banner = $this->author->username;
                    $reason = (empty($params)) ? 'None Given' : implode(' ', $params);

                    $msg = "{$this->author->user->username} kicked you. | Reason: $reason";

                    $embed = $this->createEmbed([
                        'title'  => 'User '.$member->username.' was kicked',
                        'color'  => Color::WARNING,
                        'author' => [
                            'name'     => $this->app->getBotUser()->username,
                            'url'      => 'https://github.com/pazuzu156/AnubisBot',
                            'icon_url' => $this->app->getBotUser()->avatar,
                        ],
                        'fields' => [
                            [
                                'name'   => 'Kicked By',
                                'value'  => $banner,
                                'inline' => true,
                            ],
                            [
                                'name'   => 'Reason',
                                'value'  => $reason,
                                'inline' => true,
                            ],
                        ],
                    ]);

                    $ctx = $this;
                    $member->user->sendMessage($msg)->then(function () use ($channel, $member, $embed, $ctx) {
                        $ctx->guild->members->kick($member)->then(function () use ($channel, $embed) {
                            $channel->sendMessage('', false, $embed);
                        });
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
                if (substr($p->first(), 0, 2) == '<@') {
                    $member = $this->member($p->first());
                } else {
                    $member = $p->first();
                }

                if (is_string($member)) {
                    $id = $member;
                    $member = $this->member('<@'.$member.'>');
                    if (!is_null($member->id)) {
                        $this->handleBan($member, $p);
                    } else {
                        if (is_numeric($id)) {
                            $bannedUsers = $this->getBannedUsers();
                            $bannedUsers[] = $id;
                            $dataFile = $this->guild->dataFile()->getAsArray();
                            $dataFile['banned_users'] = $bannedUsers;
                            $this->guild->dataFile()->write($dataFile);

                            $this->message->reply('User ID "'.$id.'" added into ban list to auto-ban when they join');
                        }
                    }
                } else {
                    $this->handleBan($member, $p);
                }
            }
        }
    }

    /**
     * Handles the banning of a user with a nice embed.
     *
     * @param \Core\Wrappers\Parts\Member $member
     * @param \Core\Command\Parameters    $p
     *
     * @return void
     */
    private function handleBan($member, Parameters $p)
    {
        if ($member) {
            $banner = $this->author->user->username;
            $params = $p->all();
            array_shift($params);

            $reason = (empty($params)) ? 'None Given' : implode(' ', $params);
            $msg = "$banner banned you. | Reason: $reason";

            $embed = $this->createEmbed([
                'title'  => 'User '.$member->username.' was banned',
                'color'  => Color::WARNING,
                'author' => [
                    'name'     => $this->app->getBotUser()->username,
                    'url'      => 'https://github.com/pazuzu156/AnubisBot',
                    'icon_url' => $this->app->getBotUser()->avatar,
                ],
                'fields' => [
                    [
                        'name'   => 'Banned By',
                        'value'  => $banner,
                        'inline' => true,
                    ],
                    [
                        'name'   => 'Reason',
                        'value'  => $reason,
                        'inline' => true,
                    ],
                ],
            ]);
            $channel = $this->getBotSpam();
            $ctx = $this;

            $member->user->sendMessage($msg)->then(function () use ($channel, $member, $embed, $ctx) {
                $ctx->banUser($member, 50)->then(function () use ($channel, $embed) {
                    $channel->sendMessage('', false, $embed);
                });
            });
        } else {
            $this->message->reply('Sorry, but that user could not be found.');
        }
    }
}
