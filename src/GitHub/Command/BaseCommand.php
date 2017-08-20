<?php

namespace GitHub\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseCommand
 * @package GitHub\Command
 */
class BaseCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $name  = 'github:' . strtolower(implode(':', array_slice(preg_split('/(?=[A-Z])/',get_class($this)), -3, -1)));
        $this->setName($name);
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if ($this->container === null) {
            throw new \LogicException('The container cannot be retrieved as it was not previously set.');
        }

        return $this->container;
    }
}