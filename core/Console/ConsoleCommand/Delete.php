<?php

namespace Core\Console\ConsoleCommand;

use Core\Utils\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            new InputOption('part', 'p', InputOption::VALUE_REQUIRED, 'Specify where the console command is located (app|core)'),
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
        $part = $input->getOption('part');

        $exp = explode('\\', $name);
        $dTree = ''; // Directory tree for generating folders for the command

        if (!is_null($part)) {
            switch (strtolower($part)) {
                case 'core':
                    $dTree = base_path().'/core/Console';
                    break;
                case 'app':
                    $dTree = console_path();
                    break;
                default:
                    throw new \Exception('Invalid part option! Use app or core');
            }
        } else {
            $dTree = console_path();
        }

        for ($i = 0; $i < count($exp); $i++) {
            if ($i == (count($exp) - 1)) {
                $name = $exp[$i];
            } else {
                $dTree .= '/'.$exp[$i];
            }
        }

        $cpath = $dTree;
        $filename = $name.'.php';
        $filepath = $cpath.'/'.$filename;

        if (File::exists($filepath)) {
            File::delete($filepath, true);
            $output->writeln("<info>Console command: $name was deleted</>");
            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Consone command: $name doesn't exist!</>");
        }
    }
}
