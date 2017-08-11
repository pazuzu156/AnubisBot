# AnubisBot
[![StyleCI](https://styleci.io/repos/87753072/shield?branch=master)](https://styleci.io/repos/87753072)
[![Latest Stable Version](https://poser.pugx.org/pazuzu156/anubisbot/v/stable?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Latest Unstable Version](https://poser.pugx.org/pazuzu156/anubisbot/v/unstable?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Total Downloads](https://poser.pugx.org/pazuzu156/anubisbot/downloads?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![License](https://poser.pugx.org/pazuzu156/anubisbot/license?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Bot Status](https://api.kalebklein.com/anubisbot/status)](https://api.kalebklein.com/anubisbot/status)

A Discord bot built in PHP

## NOTICE
### It's currently storing in my area, so expect some bot downtime for the next few hours. Sorry.

## Install
Use composer to install the latest stable version of AnubisBot

`$ composer create-project pazuzu156/anubisbot --prefer-dist`

If you want the latest codebase for AnubisBot, please use the `dev-develop` branch:

`$ composer create-project pazuzu156/anubisbot:dev-develop --stability=dev --prefer-dist`  
or  
`$ composer create-project pazuzu156/anubisbot:dev-master --stability=dev --prefer-dist`  
for the master branch

`dev-develop` is the most active branch. If you want to use `master`, `dev-develop` is merged into `master` daily, so while changes are always frequent, it's more stable than `dev-develop`

## Command Registrar
`registrar.php` holds commands to register 

A seperate .env file houses bot information  
`DEBUG` Enables/disables debugging for bot  
`LOG_TO_FILE` Whether you want to log to files or not  
`LOG_DISCORDPHP` Whether you want DiscordPHP to have logging or not  
`TOKEN` houses your bot's auth token  
`NAME` is for the name of your bot  
`DESCRIPTION` is the description of your bot  
`PREFIX` is the prefix to use for your bot (used for recognizing commands)  
`PREFIX_SPACE` tells the bot whether prefixes should also include a space  
`BOTSPAM_CHANNEL_ID` the channel id that AnubisBot's auto-messages should go

## Commands/Aliases
Commands are the bot commands. Commands can have sub commands, which act as different bits of the master command. Each command can also be aliased.

### Commands
To create a new command, run: `$ php cli make:command MyNewCommand`. This will create a new command in `app/commands/MyNewCommand.php`.

To create a new command alias, run: `$ php cli make:alias MyNewAlias`. This will create a new command alias in `app/aliases/MyNewAlias.php`.

Commands are defined by extending the base `Command` class. A command also has 2 required properties. One of which MUST have content within it.

`protected $name = 'mynewcommand'` defined the command's name. Or what the user will use in Discord to expect a reaction from the bot.  
`protected $description = ''` gives a command it's description. Used in `!help`

### Sub Commands
Sub commands are defined by defining a public method within your command class. In your `MyNewCommand.php` define the sub command `subby` by:   
```php
/**
 * This block is my description ;).
 *
 * @param \Core\Parameters $p
 *
 * @return void
 */
public function subby(Parameters $p)
{
    $this->message->reply('I am a sub command!');
}
```

While commands have their `$description` property to define their `!help` descriptions, sub commands utilize their PHPDoc blocks for their descriptions.

### Command Description Variables
Commands and sub commands have description variables that can be used to give your bot specific info (as well as command inheritance) in `!help` descriptions

`{PREFIX}` is replaced with your bot's prefix  
`{NAME}` is replaced with your bot's name  
`{VERSION}` is replaced with the bot's current version  
`{INHERIT_COMMAND_SUBCOMMAND}` inherits a command's (and sub command's) description

`{INHERIT_COMMAND_SUBCOMMAND}` is different as the variable is also varying. `COMMAND` is replaced with the command's `$name` property value and `SUBCOMMAND` is replaced with the sub command's method name.

### Aliases
Aliases have an extra required property (which is now public)

`public $alias = 'mynewcommand'` Tells the master command list that this command is used to alias the `mynewcommand` command.

Within your alias's sub command (yes, even aliases have them) Be sure to include the inheritance doc variable, and call on the class's `runCommand()` method to run the master command's command. :wink:

```php
/**
 * {INHERIT_MYNEWCOMMAND_SUBBY}
 *
 * @param \Core\Parameters $p
 *
 * @return void
 */
public function newsubby(Parameters $p)
{
    $this->runCommand('subby', $p);
}
```

### Registering Commands/Aliases
To register a command, inside `registrar.php` under `commands` add the PHP class calling method to the array:

```php
...
    'commands' => [
        ...
        App\Commands\MyNewCommand::class,
        ...
    ],
...
```

To register an alias, do the same, but only inside the `aliases` array

## Run
Run the bot with `$ php cli run`

Running the above will also run the on-start changelog message. To disable this, add `false` at the end (Unless you set `DEBUG` equal to `true` inside your `.env` file)

## Custom CLI Console Commands
AnubisBot has support for adding custom Symfony commands. Just run: `$ php cli make:console MyNewConsoleCommand` and add the command to `registrar.php` under the `console` array. Then just call `$ php cli <MYNEWCONSOLECOMMAND>` replaceing `<MYNEWCONSOLECOMMAND>` with whatever you used in the commands `$this->setName()` call.

## Invite
If you want to invite the bot instead of running it yourself, [click here](https://discordapp.com/oauth2/authorize?permissions=268823574&scope=bot&client_id=327322469197283328)

## Documentation
Documentation for AnubisBot can be found here: [https://api.kalebklein.com/anubisbot/docs/](https://api.kalebklein.com/anubisbot/docs/)
