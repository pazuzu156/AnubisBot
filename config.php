<?php

// app config

return [
    // All commands are registered here
    'commands' => [
        Commands\Cow::class,
        Commands\About::class,
        Commands\Messages::class,
        Commands\Power::class,
        Commands\UserInfo::class,
    ],

    // All command aliases are registered here
    'aliases' => [
        Aliases\Prune::class,
    ],

    // All console commands are registered here
    'console' => [
        Core\Console\Alias\Create::class,
        Core\Console\Alias\Delete::class,
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\Run::class,
    ],
];
