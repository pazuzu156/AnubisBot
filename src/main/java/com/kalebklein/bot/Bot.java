package com.kalebklein.bot;

import sx.blah.discord.api.IDiscordClient;

/**
 * Created by kaleb on 5/9/17.
 */
public class Bot
{
    public Bot()
    {
        IDiscordClient cli = BotUtils.getBuiltDiscordClient(this.getBotToken());

        cli.getDispatcher().registerListener(new MyEvents());
        cli.login();
    }

    private String getBotToken()
    {
        return new Config().get("token");
    }
}
