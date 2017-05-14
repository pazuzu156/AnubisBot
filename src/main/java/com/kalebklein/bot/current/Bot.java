package com.kalebklein.bot.current;

import net.dv8tion.jda.core.AccountType;
import net.dv8tion.jda.core.JDABuilder;
import net.dv8tion.jda.core.exceptions.RateLimitedException;

import javax.security.auth.login.LoginException;

/**
 * Created by kaleb on 5/14/17.
 */
public class Bot
{
    public static void main(String[] args)
            throws LoginException, InterruptedException, RateLimitedException
    {
        new JDABuilder(AccountType.BOT)
                .setToken(new Config().getString("token"))
                .addEventListener(new MessageListener())
                .addEventListener(new OnReadyListener())
                .buildBlocking();
    }
}
