<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tdc_pdo_messenger_transport');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('table_name')
                    ->defaultValue('messenger_messages')
                    ->info('The table name to use for the messenger.')
                ->end()
                ->scalarNode('pdo_service')
                    ->defaultNull()
                    ->info('The service ID of the PDO instance to use.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
