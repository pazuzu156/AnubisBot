<?php

// app config

return [
    'commands' => [
        Commands\Uptime::class,
    ],
    'console' => [
        Core\Console\Command\Create::class,
        // Core\Console\Command\Delete::class,
        Core\Console\Run::class,
    ],
];
