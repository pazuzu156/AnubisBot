<?php

namespace Core\Console;

use Core\Utils\File;
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
        File::move(logs_path().'/.gitkeep', base_path().'/.gitkeep_logs');
        $handle = opendir(logs_path());
        $ignore = ['.', '..'];

        while ($file = readdir($handle)) {
            if (!in_array($file, $ignore)) {
                File::delete(logs_path().'/'.$file);
                $output->writeln('<info>Clearing out '.$file.'</>');
            }
        }

        File::move(base_path().'/.gitkeep_logs', logs_path().'/.gitkeep');
        $output->writeln('<info>Logs directory cleaned out successfully</>');
    }
}
