package com.kalebklein.bot;

import sx.blah.discord.api.events.EventSubscriber;
import sx.blah.discord.handle.impl.events.guild.channel.message.MessageReceivedEvent;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

/**
 * Created by kklei on 2017-05-09.
 */
public class MyEvents
{
    /**
     * Processes messages on event call.
     *
     * @param e
     */
    @EventSubscriber
    public void onMessageReceived(MessageReceivedEvent e)
    {
        String[] argArray = e.getMessage().getContent().split(" ");

        if (argArray.length == 0) return;

        if (!argArray[0].startsWith(BotUtils.BOT_PREFIX)) return;

        String commandStr = argArray[0].substring(1);

        List<String> argList = new ArrayList<>(Arrays.asList(argArray));
        argList.remove(0);

        switch (commandStr) {
            case "test":
                testCommand(e, argList);
                break;
        }
    }

    /**
     * Test command.
     *
     * @param e
     * @param argList
     */
    private void testCommand(MessageReceivedEvent e, List<String> argList)
    {
        BotUtils.sendMessage(e.getChannel(), "Test: " + argList);
    }
}
