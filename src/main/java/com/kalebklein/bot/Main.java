package com.kalebklein.bot;

import sx.blah.discord.api.IDiscordClient;

public class Main
{
    /**
     * Ctor.
     *
     * @param args
     */
    public static void main(String[] args) {
        IDiscordClient cli = BotUtils.getBuiltDiscordClient("YOUR_BOT_ID");

        cli.getDispatcher().registerListener(new MyEvents());
        cli.login();
    }
}
