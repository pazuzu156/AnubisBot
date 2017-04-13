<?php

namespace Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    /**
     * Configure Symfony Command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run')
        ->setDescription('Runs the bot')
        ->setDefinition(new InputDefinition([
            new InputArgument('startup_message', InputArgument::OPTIONAL, 'To run with on-start changelog. True | False* (*default)'),
        ]));
    }

    /**
     * Execute console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $msg = $input->getArgument('startup_message');
        $bool = $input->getArgument('startup_message');

        switch($bool) {
            case 'true':
            $bool = true;
            break;
            case 'false':
            $bool = false;
            break;
        }
        
        require_once base_path().'/bot.php';
    }
}
