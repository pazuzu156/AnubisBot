package com.kalebklein.bot.current;

import net.dv8tion.jda.core.AccountType;
import net.dv8tion.jda.core.JDA;
import net.dv8tion.jda.core.JDABuilder;
import net.dv8tion.jda.core.OnlineStatus;
import net.dv8tion.jda.core.entities.*;
import net.dv8tion.jda.core.events.message.MessageReceivedEvent;
import net.dv8tion.jda.core.exceptions.RateLimitedException;
import net.dv8tion.jda.core.hooks.ListenerAdapter;

import javax.security.auth.login.LoginException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

/**
 * Created by kaleb on 5/9/17.
 */
public class MessageListener extends ListenerAdapter
{
    @Override
    public void onMessageReceived(MessageReceivedEvent evt) {
//        super.onMessageReceived(event);
        JDA jda = evt.getJDA();

        ArrayList<String> commands = new ArrayList<String>();
        commands.add("ping");

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
            pc.sendMessage("Hello").queue();
            System.out.printf("[PRIV]<%s>: %s\n", author.getName(), msg);
        }

        if (!bot) {
            for (Iterator<String> i = commands.iterator(); i.hasNext();) {
                String cmd = i.next();

                if (MessageFilter.isCommand(msg, cmd)) {
                    channel.sendMessage("pong!").queue();
                }
            }
        }
    }
}
