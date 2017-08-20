<?php
namespace Core\Console;
use GitHub\Command\BaseCommand;
use GitHub\DependencyInjection\GitHubExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param string $name
     * @param string $version
     * @param string $settingsFile
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN', $settingsFile = __DIR__ . '/../../../app/config.yml')
    {
        $extension = new GitHubExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($extension);
        $this->container->loadFromExtension($extension->getAlias());

        $loader = new YamlFileLoader($this->container, new FileLocator([
            __DIR__ . '/../../Resources/config',
            dirname($settingsFile),
        ]));

        if (!file_exists($settingsFile)) {
            throw new LogicException('Could not find settings file in path ' . $settingsFile);
        }

        $loader->load(basename($settingsFile));


        $this->container->compile();


        parent::__construct($name, $version);
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[] An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $commands = [];
        /**
         * @var BaseCommand $command
         */
        foreach ($this->container->findTaggedServiceIds('console.command') as $commandId => $command) {
            $command = $this->container->get($commandId);
            $commands[] = $command;

            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
        }
        return $commands;
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }
}