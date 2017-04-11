<?php

namespace Commands;

use Core\Command;
use Core\Parameters;
use Curl\Curl;

class GetID extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'getid';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Gets the user ID or Steam64 ID';

    /**
     * Default command method.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->me();
    }

    /**
     * Sends a DM with your Discord ID.
     * Same as '{PREFIX}getid'.
     *
     * @return void
     */
    public function me()
    {
        $me = $this->author->user;
        $me->sendMessage("Your Discord ID is: `{$me->id}`");
    }

    /**
     * Returns the given user's Steam64 ID.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function steam(Parameters $p)
    {
        $curl = new Curl();
        $user = $p->first();

        if (is_null($user)) {
            $this->message->reply('You forgot to give the username!');
        } else {
            $curl->get("https://api.kalebklein.com/steam/public/getid?username=$user");

            if ($curl->response->error) {
                $this->message->reply("Error getting user ID: {$curl->response->message}");
            } else {
                $this->message->reply("$user's Steam64 ID is: {$curl->response->steam64}");
            }
        }
    }
}
