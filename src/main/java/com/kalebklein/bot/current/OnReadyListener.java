package com.kalebklein.bot.current;

import net.dv8tion.jda.core.events.Event;
import net.dv8tion.jda.core.events.ReadyEvent;
import net.dv8tion.jda.core.hooks.EventListener;

/**
 * Created by kaleb on 5/14/17.
 */
public class OnReadyListener implements EventListener
{
    @Override
    public void onEvent(Event event)
    {
        if (event instanceof ReadyEvent)
        {
            GameSetter setter = new GameSetter(event.getJDA());
            String game = new Config().getString("defaultPresence");

            setter.updatePresence(game);
        }
    }
}
