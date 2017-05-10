package com.kalebklein.bot.current;

import net.dv8tion.jda.core.AccountType;
import net.dv8tion.jda.core.JDA;
import net.dv8tion.jda.core.JDABuilder;
import net.dv8tion.jda.core.entities.ChannelType;
import net.dv8tion.jda.core.events.message.MessageReceivedEvent;
import net.dv8tion.jda.core.exceptions.RateLimitedException;
import net.dv8tion.jda.core.hooks.ListenerAdapter;

import javax.security.auth.login.LoginException;

/**
 * Code stolen straight from JDA's readme (Will be gone soon)
 * Created by kaleb on 5/9/17.
 */
public class Bot extends ListenerAdapter
{
    public static void main(String[] args)
            throws LoginException, InterruptedException, RateLimitedException
    {
        JDA jda = new JDABuilder(AccountType.BOT)
                .setToken(new Config().get("token"))
                .addEventListener(new Bot())
                .buildBlocking();
    }

    @Override
    public void onMessageReceived(MessageReceivedEvent event) {
//        super.onMessageReceived(event);
        if (event.isFromType(ChannelType.PRIVATE)) {
            System.out.printf("[PM] %s: %s\n",
                    event.getAuthor().getName(),
                    event.getMessage().getContent());
        } else {
            System.out.printf("[%s][%s] %s: %s\n",
                    event.getGuild().getName(),
                    event.getTextChannel().getName(),
                    event.getMember().getEffectiveName(),
                    event.getMessage().getContent());
        }
    }
}
