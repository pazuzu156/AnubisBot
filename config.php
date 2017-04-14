<?php

// app config

return [
    // All commands are registered here
    'commands' => [
        Commands\Cow::class,
        Commands\About::class,
        Commands\Messages::class,
        Commands\Prune::class,
        Commands\UserInfo::class,
    ],

    // All console commands are registered here
    'console' => [
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\Run::class,
    ],
];
