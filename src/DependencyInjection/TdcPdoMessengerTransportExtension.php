<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Tdc\PdoMessengerTransport\Transport\PdoTransportFactory;

class TdcPdoMessengerTransportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = new Definition(PdoTransportFactory::class);
        $definition->setAutoconfigured(true);
        $definition->setAutowired(true);
        $definition->addTag('messenger.transport_factory');
        $definition->setArgument('$tableName', $config['table_name']);

        if (isset($config['pdo_service']) && $config['pdo_service']) {
            $definition->setArgument('$pdo', new Reference($config['pdo_service']));
        }

        $container->setDefinition('tdc_pdo_messenger_transport.transport_factory', $definition);
        $container->setAlias(PdoTransportFactory::class, 'tdc_pdo_messenger_transport.transport_factory');
    }

    public function getAlias(): string
    {
        return 'tdc_pdo_messenger_transport';
    }
}
