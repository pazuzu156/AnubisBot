<?php

namespace Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearLogs extends Command
{
    /**
     * Configure Symfony Command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('clearlogs')
        ->setDescription('Clears out the logs directory');
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
        copy(logs_path().'/.gitignore', base_path().'/.gitignore_logs');
        unlink(logs_path().'/.gitignore');
        $handle = opendir(logs_path());
        $ignore = ['.', '..'];

        while ($file = readdir($handle)) {
            if (!in_array($file, $ignore)) {
                unlink(logs_path().'/'.$file);
                $output->writeln('<info>Clearing out '.$file.'</>');
            }
        }

        copy(base_path().'/.gitignore_logs', logs_path().'/.gitignore');
        unlink(base_path().'/.gitignore_logs');
        $output->writeln('<info>Logs directory cleaned out successfully</>');
    }
}
