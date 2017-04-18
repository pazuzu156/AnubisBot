<?php

namespace Core\Console\ConsoleCommand;

use Core\Wrappers\FileSystemWrapper as File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    /**
     * Configure Symfony Command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('make:console')
        ->setDescription('Creates a new console command template')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the console command class to create (CammelCase please)'),
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
        $nametolower = strtolower($name);

        $content = <<<EOF
<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class $name extends Command
{
    /**
     * Configure Symfony Command.
     *
     * @return void
     */
    protected function configure()
    {
        \$this->setName('$nametolower');
    }

    /**
     * Execute console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   \$input
     * @param \Symfony\Component\Console\Output\OutputInterface \$output
     *
     * @return void
     */
    protected function execute(InputInterface \$input, OutputInterface \$output)
    {
        \$output->writeln("<info>This is your $name command!</>");
    }
}

EOF;

        $cpath = console_path();
        $filename = $name.'.php';
        $filepath = $cpath.'/'.$filename;

        if (!File::exists($filepath)) {
            File::write($filepath, $content);

            $output->writeln("<info>Console command: $name created</>");

            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Console command: $name already exists!</>");
        }
    }
}
