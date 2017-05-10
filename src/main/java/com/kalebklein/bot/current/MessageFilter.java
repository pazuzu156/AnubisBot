package com.kalebklein.bot.current;

/**
 * Created by kaleb on 5/9/17.
 */
public class MessageFilter
{
    public static boolean isCommand(String message, String filter)
    {
        Config c = new Config();
        String prefix = c.get("prefix");
        boolean prefixSpace = c.getBool("prefix_space");

        if (prefixSpace) {
            prefix = prefix + " ";
        }

        return message.startsWith(prefix + filter);
    }
}
