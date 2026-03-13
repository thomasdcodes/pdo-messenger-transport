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

        // Ensure the root node is allowed to have extra keys or is more flexible
        $rootNode
            ->children()
                ->scalarNode('table_name')
                    ->defaultValue('messenger_messages')
                ->end()
                ->scalarNode('pdo_service')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
