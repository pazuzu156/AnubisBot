# AnubisBot
[![StyleCI](https://styleci.io/repos/87753072/shield?branch=master)](https://styleci.io/repos/87753072)
[![Latest Stable Version](https://poser.pugx.org/pazuzu156/anubisbot/v/stable?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Latest Unstable Version](https://poser.pugx.org/pazuzu156/anubisbot/v/unstable?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Total Downloads](https://poser.pugx.org/pazuzu156/anubisbot/downloads?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![License](https://poser.pugx.org/pazuzu156/anubisbot/license?format=flat-square)](https://packagist.org/packages/pazuzu156/anubisbot)
[![Bot Status](https://api.kalebklein.com/anubisbot_status)](https://api.kalebklein.com/anubisbot_status)

A Discord bot built in PHP

## Install
Use composer to install the latest stable version of AnubisBot

`$ composer create-project pazuzu156/anubisbot --prefer-dist`

If you want the latest codebase for AnubisBot, please use the `dev-develop` branch:

`$ composer create-project pazuzu156/anubisbot:dev-develop --stability=dev --prefer-dist`  
or  
`$ composer create-project pazuzu156/anubisbot:dev-master --stability=dev --prefer-dist`  
for the master branch

`dev-develop` is the most active branch. If you want to use `master`, `dev-develop` is merged into `master` daily, so while changes are always frequent, it's more stable than `dev-develop`

## Config
`config.php` holds commands to register 

A seperate .env file houses bot information  
`TOKEN` houses your bot's auth token  
`NAME` is for the name of your bot  
`DESCRIPTION` is the description of your bot  
`PREFIX` is the prefix to use for your bot (used for recognizing commands)  
`PREFIX_SPACE` tells the bot whether prefixes should also include a space  
`BOTSPAM_CHANNEL_ID` the channel id that AnubisBot's auto-messages should go

## Command
Create a command with `$ php cli command:make <COMMAND>`

Sub commands are basically public methods within your command class. While the base command has `protected $description` as it's description for `!help`, sub commands get their descriptions from their method's docblocks.

Command descriptions (including sub commands) can house variables using `{VAR_NAME}`

`PREFIX`, `NAME`, `INHERIT_COMMAND_SUBCOMMAND`, and `VERSION` are the only suppored ones at the moment though..

`{PREFIX}` is replaced with your bot's prefix  
`{NAME}` is replaced with your bot's name  
`{VERSION}` is replaced with the bot's current version  
`{INHERIT_COMMAND_SUBCOMMAND}` inherits a command's (and sub command's) description

To inherit a description, `COMMAND` is the command name (not the class itself) and `SUBCOMMAND` is the sub command to get the info from.

## Run
Run the bot with `$ php cli run`

Running the above will also run the on-start changelog message. To disable this, add `false` at the end

## Invite
If you want to invite the bot instead of running it yourself, [click here](https://discordapp.com/oauth2/authorize?client_id=302580156176924672&scope=bot&permissions=36957190)
