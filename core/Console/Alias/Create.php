<?php

namespace Core\Console\Alias;

use Core\Wrappers\File;
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
        $this->setName('make:alias')
        ->setDescription('Creates a new alias template')
        ->setDefinition(new InputDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The name of the alias class to create (CammelCase please)'),
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

namespace App\Aliases;

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
    public \$alias = '';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters $p
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

        $cpath = aliases_path();
        $filename = $name.'.php';
        $filepath = $cpath.'/'.$filename;

        if (!File::exists($filepath)) {
            File::write($filepath, $content);

            $output->writeln("<info>Alias: $name created</>");

            shell_exec('composer dump-autoload -o');
        } else {
            $output->writeln("<error>Alias: $name already exists!</>");
        }
    }
}
