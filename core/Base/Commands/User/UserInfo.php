<?php

namespace Core\Base\Commands\User;

use Carbon\Carbon;
use Core\Command\Command;
use Core\Command\Parameters;
use Core\Utils\Color;
use Discord\Parts\Embed\Embed;
use Discord\Parts\User\Member;
use GuzzleHttp\Client;

class UserInfo extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'info';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Get user info for yourself or an @Mention';

    /**
     * Default command method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        if ($p->count()) {
            $user = $this->member($p->first());
            $this->channel->sendMessage('', false, $this->getUserInfo($user));
        } else {
            $this->me();
        }
    }

    /**
     * Returns your user info in a nice little box.
     *
     * @return void
     */
    public function me()
    {
        $this->channel->sendMessage('', false, $this->getUserInfo($this->author));
    }

    /**
     * Gets your ID and sends it to you in a DM.
     *
     * @return void
     */
    public function getid()
    {
        $this->author->user->sendMessage("Your Discord ID is: `{$this->author->user->id}`");
    }

    /**
     * Returns the given user's Steam64 ID.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function steam(Parameters $p)
    {
        $user = $p->first();

        if (!is_null($user)) {
            $client = new Client();
            $response = $client->get('https://api.kalebklein.com/steam/public/getid?username='.$user);

            $content = @json_decode($response->getBody()->getContents());

            if ($content->error) {
                $this->message->reply("Error getting user ID: {$content->message}");
            } else {
                $this->message->reply("$user's Steam64 ID is: {$content->steam64}");
            }
        } else {
            $this->message->reply('You forgot to give the username!');
        }
    }

    /**
     * Gets the user info and generates an Embed box with that info.
     *
     * @param \Discord\Parts\User\Member $member
     *
     * @return \Discord\Parts\Embed\Embed
     */
    private function getUserInfo(Member $member)
    {
        $user = $member->user;
        $bot = $this->app->getBotUser();

        $userRolesArr = [];
        foreach ($member->roles as $role) {
            $userRolesArr[] = $role->name;
        }

        $roles = implode(', ', $userRolesArr);
        $roles = rtrim($roles, ', ');

        if (is_null($member->nick)) {
            $nick = 'No nickname given';
        } else {
            $nick = $member->nick;
        }

        $carbon = carbon(strtotime($member->joined_at));
        $date = $carbon->toRfc2822String();

        $fields = [
            [
                'name'  => 'Name',
                'value' => "{$user->username}#{$user->discriminator}",
            ],
            [
                'name'  => 'Nickname',
                'value' => $nick,
            ],
            [
                'name'  => 'ID',
                'value' => $user->id,
            ],
            [
                'name'   => 'Joined Server',
                'value'  => "$date ({$carbon->diffForHumans()})",
                'inline' => false,
            ],
        ];

        if (!is_null($roles) && $roles !== '') {
            $fields[] = [
                'name'  => 'Roles',
                'value' => $roles,
            ];
        }

        if (!is_null($member->game->name)) {
            $fields[] = [
                'name'  => 'Currently Playing',
                'value' => $member->game->name,
            ];
        }

        return $this->createEmbed([
            'color'     => Color::INFO,
            'thumbnail' => $this->embedImage($user->avatar),
            'fields'    => $fields,
        ]);
    }
}
