<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Tdc\PdoMessengerTransport\Transport\PdoTransportFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class TdcPdoMessengerTransport extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $definition = new Definition(PdoTransportFactory::class);
        $definition->setAutoconfigured(true);
        $definition->setAutowired(true);
        $definition->setPublic(true);
        $definition->addTag('messenger.transport_factory');

        $tableName = $config['table_name'] ?? 'messenger_messages';
        $pdoService = $config['pdo_service'] ?? null;

        $definition->setArgument('$tableName', $tableName);

        if ($pdoService) {
            $definition->setArgument('$pdo', new Reference($pdoService));
        }

        $builder->setDefinition(PdoTransportFactory::class, $definition);
    }
}