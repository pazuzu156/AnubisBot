<?php

namespace Core\Console\Command;

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
        $this->setName('command:make')
        ->setDescription('Creates a new command template')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the command class to create (CammelCase please)'),
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

namespace Commands;

use Core\Command;
use Core\Parameters;

class $name extends Command
{
    /**
     * {@inheritdoc}
     */
    protected \$name = '$nametolower';

    /**
     * {@inheritdoc}
     */
    protected \$description = '';

    /**
     * Default command method.
     *
     * @param \Core\Parameters \$p
     *
     * @return void
     */
    public function index(Parameters \$p)
    {
        // Default command method
    }

    // Place your methods here
}

EOF;

        $cpath = base_path().'/commands/';
        $filename = $name.'.php';
        $filepath = $cpath.$filename;

        if (!file_exists($filepath)) {
            $file = fopen($filepath, 'w');
            fwrite($file, $content);
            fclose($file);

            $output->writeln("<info>Command: $name created</>");

            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Command: $name already exists!</>");
        }
    }
}
