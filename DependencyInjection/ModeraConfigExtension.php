<?php

namespace Modera\ConfigBundle\DependencyInjection;

use Modera\ConfigBundle\ModeraConfigBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraConfigExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        /*
        $kernelBundles = $container->getParameter('kernel.bundles');
        if (isset($kernelBundles['ModeraSecurityBundle']) && null == $config['owner_entity']) {
            $config['owner_entity'] = 'Modera\SecurityBundle\Entity\User';
        }
        */

        if (\is_string($config['owner_entity'] ?? null)) {
            $listener = $container->getDefinition('modera_config.listener.owner_relation_mapping_listener');

            $listener->addTag('doctrine.event_listener', [
                'event' => 'loadClassMetadata',
            ]);
        }

        $container->setParameter(ModeraConfigBundle::CONFIG_KEY, $config);

        if (\class_exists('Symfony\Component\Console\Application')) {
            try {
                $loader->load('console.xml');
            } catch (\Exception $e) {
            }
        }
    }
}
