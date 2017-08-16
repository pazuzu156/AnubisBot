<?php

// Commands register

return [
    // All commands are registered here
    'commands' => [
        // All built-in commands are registered here
        Core\Base\Commands\About::class,
        Core\Base\Commands\BotMod::class,
        Core\Base\Commands\Cow::class,
        Core\Base\Commands\Help::class,
        Core\Base\Commands\Messages::class,
        Core\Base\Commands\Power::class,
        Core\Base\Commands\User\Roles::class,
        Core\Base\Commands\User\User::class,
        Core\Base\Commands\User\UserInfo::class,

        // Register any of your custom commands here
        // Core\Base\Commands\Youtube::class, // Testing class
    ],

    // All command aliases are registered here
    'aliases' => [
        // All built-in command aliases are registered here
        Core\Base\Aliases\About\Changes::class,
        Core\Base\Aliases\About\Source::class,
        Core\Base\Aliases\About\Uptime::class,
        Core\Base\Aliases\Roles\JoinRole::class,
        Core\Base\Aliases\Roles\LeaveRole::class,
        Core\Base\Aliases\Roles\ListRoles::class,
        Core\Base\Aliases\Prune::class,
        Core\Base\Aliases\User\BanUser::class,
        Core\Base\Aliases\User\KickUser::class,

        // Register any of your custom command aliases here
    ],

    // All console commands are registered here
    'console' => [
        // All built-in console commands are registered here
        Core\Console\Alias\Create::class,
        Core\Console\Alias\Delete::class,
        Core\Console\ClearLogs::class,
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\ConsoleCommand\Create::class,
        Core\Console\ConsoleCommand\Delete::class,
        Core\Console\Run::class,

        // Register any of your custom console commands here
    ],
];
