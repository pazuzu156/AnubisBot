<?php

// app config

return [
    // All commands are registered here
    'commands' => [
        // All built-in commands are registered here
        Core\Base\Commands\Cow::class,
        Core\Base\Commands\About::class,
        Core\Base\Commands\Messages::class,
        Core\Base\Commands\Power::class,
        // Core\Base\Commands\Roles::class, // Will uncomment when roles are done
        Core\Base\Commands\UserInfo::class,

        // Register any of your custom commands here
        // App\Commands\TestCommand::class,
    ],

    // All command aliases are registered here
    'aliases' => [
        // All built-in command aliases are registered here
        Core\Base\Aliases\Prune::class,

        // Register any of your custom command aliases here
        // App\Aliases\TestAlias::class,
    ],

    // All console commands are registered here
    'console' => [
        // All built-in console commands are registered here
        Core\Console\ClearLogs::class,
        Core\Console\Run::class,
        Core\Console\Alias\Create::class,
        Core\Console\Alias\Delete::class,
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\ConsoleCommand\Create::class,
        Core\Console\ConsoleCommand\Delete::class,

        // Register any of your custom console commands here
        // App\Console\TestConsoleCommand::class,
    ],
];
