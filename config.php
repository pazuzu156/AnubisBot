<?php

// app config

return [
    // All commands are registered here
    'commands' => [
        // All built-in commands are registered here
        Core\Commands\Cow::class,
        Core\Commands\About::class,
        Core\Commands\Messages::class,
        Core\Commands\Power::class,
        Core\Commands\UserInfo::class,

        // Register any of your custom commands here

    ],

    // All command aliases are registered here
    'aliases' => [
        // All built-in command aliases are registered here
        Core\Commands\Aliases\Prune::class,

        // Register any of your custom command aliases here

    ],

    // All console commands are registered here
    'console' => [
        // All built-in console commands are registered here
        Core\Console\Alias\Create::class,
        Core\Console\Alias\Delete::class,
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\Run::class,

        // Register any of your custom console commands here

    ],
];
