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
        $definition = new Definition(PdoTransportFactory::class);
        $definition->setAutoconfigured(true);
        $definition->setAutowired(true);
        $definition->addTag('messenger.transport_factory');

        $tableName = 'messenger_messages';
        $pdoService = null;

        // Manual merge to avoid "Unrecognized option" if Configuration.php is bypassed
        foreach ($configs as $config) {
            if (isset($config['table_name'])) {
                $tableName = $config['table_name'];
            }
            if (isset($config['pdo_service'])) {
                $pdoService = $config['pdo_service'];
            }
        }

        $definition->setArgument('$tableName', $tableName);

        if ($pdoService) {
            $definition->setArgument('$pdo', new Reference($pdoService));
        }

        $container->setDefinition(PdoTransportFactory::class, $definition);
        $container->setAlias('tdc_pdo_messenger_transport.transport_factory', PdoTransportFactory::class);
    }

    public function getAlias(): string
    {
        // Explicitly set alias to match the config root name
        return 'tdc_pdo_messenger_transport';
    }
}
