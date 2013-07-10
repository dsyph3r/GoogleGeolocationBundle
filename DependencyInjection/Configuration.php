<?php

namespace Google\GeolocationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('google_geolocation');

        $supportedDrivers = array('orm', 'mongodb');

        $rootNode
		        ->children()
        			->scalarNode('db_driver')
        				->validate()
        					->ifNotInArray($supportedDrivers)
        					->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
        				->end()
        				->cannotBeOverwritten()
        				->isRequired()
        				->cannotBeEmpty()
        			->end()
        			->scalarNode('daily_limit')->defaultValue(2500)->end()
        			->scalarNode('cache_lifetime')->defaultValue(24)->end()
        		->end()
        		;

        return $treeBuilder;
    }
}
