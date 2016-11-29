<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\Factory\AdminFactoryInterface;
use Symfony\Cmf\Bundle\SeoBundle\CmfSeoBundle;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class CmfSonataAdminIntegrationExtension extends Extension
{
    /**
     * @var AdminFactoryInterface[]
     */
    private $factories;

    /**
     * @param null|AdminFactoryInterface[] $factories A list of Admin factories
     */
    public function __construct(array $factories = null)
    {
        if (null !== $factories) {
            $this->factories = $factories;
        } else {
            $bundles = [
                CmfSeoBundle::class => new Factory\SeoAdminFactory(),
            ];

            foreach ($bundles as $bundleFqcn => $factory) {
                if (class_exists($bundleFqcn)) {
                    $this->registerAdminFactory($factory);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->factories);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadBundles($config['bundles'], $loader, $container);
    }

    private function loadBundles(array $config, XmlFileLoader $loader, $container)
    {
        foreach ($this->factories as $name => $factory) {
            if ($this->isConfigEnabled($container, $config[$name])) {
                $factory->create($config[$name], $container, $loader);
            }
        }
    }

    /**
     * Registers an admin factory.
     *
     * This method can be called in a bundle's build() method in order to add 
     * new admin integrations.
     *
     * @param AdminFactoryInterface $factory
     */
    public function registerAdminFactory(AdminFactoryInterface $factory)
    {
        $this->factories[$factory->getKey()] = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/sonata-admin';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }
}
