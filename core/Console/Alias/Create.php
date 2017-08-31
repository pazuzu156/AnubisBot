<?php

namespace Core\Console\Alias;

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
        $this->setName('make:alias')
        ->setDescription('Creates a new alias template')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the alias class to create (CammelCase please)'),
            new InputOption('part', 'p', InputOption::VALUE_REQUIRED, 'Specify where to generate the alias (app|core)'),
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
        $namespace = 'App\\Aliases'; // By default, leave it at the app/aliases directory
        $dTree = ''; // Directory tree for generating folders for the alias

        if (!is_null($part)) {
            switch (strtolower($part)) {
                case 'core':
                    $namespace = 'Core\\Base\\Aliases';
                    $dTree = base_path().'/core/Base/Aliases';
                    break;
                case 'app':
                    $dTree = aliases_path();
                    break;
                default:
                    throw new \Exception('Invalid part option! Use app or core');
            }
        } else {
            $dTree = aliases_path();
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

use Core\Command\Alias;
use Core\Command\Parameters;

class $name extends Alias
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
    public \$alias = '';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters \$p
     *
     * @return void
     */
    public function index(Parameters \$p)
    {
        // Default alias method
    }

    // Place your methods here
}

EOF;

        $cpath = $dTree;
        $filename = $name.'.php';
        $filepath = $cpath.'/'.$filename;

        if (!File::exists($filepath)) {
            File::writeTo($filepath, $content);

            $output->writeln("<info>Alias: $name created</>");

            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Alias: $name already exists!</>");
        }
    }
}
