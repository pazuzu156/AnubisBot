<?php

namespace Core\Console\Command;

use Core\Utils\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $this->setName('make:command')
        ->setDescription('Creates a new command template')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the command class to create (CammelCase please)'),
            new InputOption('part', 'p', InputOption::VALUE_REQUIRED, 'Specify where to generate the command (app|core)'),
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
        $namespace = 'App\\Commands'; // By default, leave it at the app/commands directory
        $dTree = ''; // Directory tree for generating folders for the command

        if (!is_null($part)) {
            switch (strtolower($part)) {
                case 'core':
                    $namespace = 'Core\\Base\\Commands';
                    $dTree = base_path().'/core/Base/Commands';
                    break;
                case 'app':
                    $dTree = commands_path();
                    break;
                default:
                    throw new \Exception('Invalid part option! Use app or core');
            }
        } else {
            $dTree = commands_path();
        }

        for ($i = 0; $i < count($exp); $i++) {
            if ($i == (count($exp) - 1)) {
                $name = $exp[$i];
                $nametolower = strtolower($name);
            } else {
                $namespace .= '\\'.$exp[$i];
                $dTree .= '/'.$exp[$i];
            }
        }

        if (!file_exists($dTree)) {
            mkdir($dTree, 0777, true);
        }

        $content = <<<EOF
<?php

namespace $namespace;

use Core\Command\Command;
use Core\Command\Parameters;

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
     * {@inheritdoc}
     */
    protected \$usage = '';

    /**
     * {@inheritdoc}
     */
    protected \$hidden = false;

    /**
     * Default command method.
     *
     * @param \Core\Commands\Parameters \$p
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

        $cpath = $dTree;
        $filename = $name.'.php';
        $filepath = $cpath.'/'.$filename;

        if (!File::exists($filepath)) {
            File::writeTo($filepath, $content);

            $output->writeln("<info>Command: $name created</>");

            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Command: $name already exists!</>");
        }
    }
}
