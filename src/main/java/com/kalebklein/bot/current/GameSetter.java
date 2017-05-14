package com.kalebklein.bot.current;

import net.dv8tion.jda.core.JDA;
import net.dv8tion.jda.core.entities.Game;

/**
 * Created by kaleb on 5/14/17.
 */
public class GameSetter
{
    private JDA _jda;

    /**
     * Ctor.
     *
     * @param jda
     */
    public GameSetter(JDA jda)
    {
        this._jda = jda;
    }

    /**
     * Updates the bot's presence.
     *
     * @param name
     * @param idle
     */
    public void updatePresence(String name, boolean idle)
    {
        Game game = new Game() {
            @Override
            public String getName() {
                return name;
            }

            @Override
            public String getUrl() {
                return null;
            }

            @Override
            public GameType getType() {
                return GameType.DEFAULT;
            }
        };

        this._jda.getPresence().setPresence(game, idle);
        System.out.printf("Updating game presence to: %s", game.getName());
    }

    /**
     * Updates the bot's presence.
     *
     * @param name
     */
    public void updatePresence(String name)
    {
        this.updatePresence(name, false);
    }
}
