<?php

namespace Core\Console\ConsoleCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends Command
{
    /**
     * Configure Symfony Command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('drop:console')
        ->setDescription('Deletes a console command')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the console command class to drop (CammelCase please)'),
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
        $name = $input->getArgument('name');

        $cpath = base_path().'/console/';
        $filename = $name.'.php';
        $filepath = $cpath.$filename;

        if (file_exists($filepath)) {
            unlink($filepath);
            $output->writeln("<info>Console command: $name was deleted</>");
            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Consone command: $name doesn't exist!</>");
        }
    }
}
