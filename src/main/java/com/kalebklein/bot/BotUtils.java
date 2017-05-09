package com.kalebklein.bot;

import sx.blah.discord.api.ClientBuilder;
import sx.blah.discord.api.IDiscordClient;
import sx.blah.discord.handle.obj.IChannel;
import sx.blah.discord.util.DiscordException;
import sx.blah.discord.util.RequestBuffer;

/**
 * Created by kklei on 2017-05-09.
 */
public class BotUtils
{
    static String BOT_PREFIX = "~";

    static IDiscordClient getBuiltDiscordClient(String token)
    {
        return new ClientBuilder().withToken(token).build();
    }

    static void sendMessage(IChannel channel, String message)
    {
        RequestBuffer.request(() -> {
            try {
                channel.sendMessage(message);
            } catch (DiscordException ex) {
                System.err.println("Message could not be send with error: ");
                ex.printStackTrace();
            }
        });
    }
}
