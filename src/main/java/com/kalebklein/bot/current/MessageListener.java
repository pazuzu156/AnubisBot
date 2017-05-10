package com.kalebklein.bot.current;

import net.dv8tion.jda.core.AccountType;
import net.dv8tion.jda.core.JDA;
import net.dv8tion.jda.core.JDABuilder;
import net.dv8tion.jda.core.entities.*;
import net.dv8tion.jda.core.events.message.MessageReceivedEvent;
import net.dv8tion.jda.core.exceptions.RateLimitedException;
import net.dv8tion.jda.core.hooks.ListenerAdapter;

import javax.security.auth.login.LoginException;

/**
 * Created by kaleb on 5/9/17.
 */
public class MessageListener extends ListenerAdapter
{
    public static void main(String[] args)
            throws LoginException, InterruptedException, RateLimitedException
    {
        JDA jda = new JDABuilder(AccountType.BOT)
                .setToken(new Config().get("token"))
                .addEventListener(new MessageListener())
                .buildBlocking();
    }

    @Override
    public void onMessageReceived(MessageReceivedEvent evt) {
//        super.onMessageReceived(event);
        JDA jda = evt.getJDA();
        long responseNumber = evt.getResponseNumber();

        User author = evt.getAuthor();
        Message message = evt.getMessage();
        MessageChannel channel = evt.getChannel();

        String msg = message.getContent();
        boolean bot = author.isBot();

        if (evt.isFromType(ChannelType.TEXT)) {
            Guild guild = evt.getGuild();
            TextChannel tc = evt.getTextChannel();
            Member member = evt.getMember();

            String name;
            if (message.isWebhookMessage()) {
                name = author.getName();
            } else {
                name = member.getEffectiveName();
            }

            System.out.printf("(%s)[%s]<%s>: %s\n", guild.getName(), tc.getName(), name, msg);
        } else if (evt.isFromType(ChannelType.PRIVATE)) {
            PrivateChannel pc = evt.getPrivateChannel();
            System.out.printf("[PRIV]<%s>: %s\n", author.getName(), msg);
        }

        if (MessageFilter.isCommand(msg, "ping")) {
            channel.sendMessage("pong!").queue();
        }
    }
}
