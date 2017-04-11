<?php

// app config

return [
    // All commands are registered here
    'commands' => [
        Commands\About::class,
        Commands\Prune::class,
    ],

    // All console commands are registered here
    'console' => [
        Core\Console\Command\Create::class,
        Core\Console\Command\Delete::class,
        Core\Console\Run::class,
    ],
];
