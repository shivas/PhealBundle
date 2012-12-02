<?php

namespace shivas\PhealBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('shivas_pheal');

        $rootNode
            ->children()
                ->booleanNode('reconfigure')
                    ->defaultFalse()
                    ->example('true or false')
                    ->info("Should factory reconfigure \\PhealConfig singleton before constructing each \\Pheal object")
                ->end()
                ->append($this->getClassExtensionDefinition('cache', '\PhealNullCache'))
                ->append($this->getClassExtensionDefinition('log', '\PhealNullLog'))
                ->append($this->getClassExtensionDefinition('archive','\PhealNullArchive'))
                ->append($this->getClassExtensionDefinition('access', '\PhealNullAccess'))
                ->scalarNode("api_base")
                    ->cannotBeEmpty()
                    ->defaultValue('https://api.eveonline.com/')
                    ->example('http://api.eveonline.com/')
                    ->info("EVE API url")
                ->end()
                ->booleanNode('api_customkeys')
                    ->defaultTrue()
                    ->example('true or false')
                    ->info('enable the new customize key system (use keyID instead of userID, etc)')
                ->end()
                ->arrayNode('additional_request_parameters')
                    ->validate()->ifNull()->thenEmptyArray()->end()
                    ->info('associative array with additional parameters that should be passed to the API on every request.')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
                ->scalarNode('http_method')
                    ->cannotBeEmpty()
                    ->defaultValue('curl')
                    ->validate()->ifNotInArray(array('curl','file'))->thenInvalid('Should be "curl" or "file"')->end()
                    ->info('which http request method should be used')
                    ->example('"curl" or "file"')
                ->end()
                ->scalarNode('http_interface_ip')
                    ->defaultFalse()
                    ->info('which outgoing ip/inteface should be used for the http request. (bool) false means use default ip address')
                    ->example('false')
                ->end()
                ->scalarNode('http_user_agent')
                    ->defaultFalse()
                    ->info('which useragent should be used for http calls. (bool) false means do not change php default')
                    ->example('false')
                ->end()
                ->booleanNode('http_post')
                    ->defaultFalse()
                    ->example('true or false')
                    ->info('should parameters be transfered in the POST body request or via GET request')
                ->end()
                ->scalarNode('http_timeout')
                    ->defaultValue(10)
                    ->info('After what time should an api call considered to as timeout?')
                    ->example(10)
                ->end()
                ->booleanNode('http_ssl_verifypeer')
                    ->defaultTrue()
                    ->example('true or false')
                    ->info('verify ssl peer (CURLOPT_SSL_VERIFYPEER)')
                ->end()
                ->scalarNode('http_keepalive')
                    ->defaultFalse()
                    ->info('reuse a http connection (keep-alive for X seconds) to lower the connection handling overhead
                    keep in mind after the script ended the connection will be closed anyway.')
                    ->example('bool|int number of seconds a connection should be kept open (bool true == 15)')
                ->end();

        return $treeBuilder;
    }


    /**
     * Generates default extension point node definition
     *
     * @param $nodename string node name
     * @param $default_class string default value for class attribute
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function getClassExtensionDefinition($nodename, $default_class)
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($nodename);

        $rootNode
            ->children()
                ->scalarNode('class')
                    ->defaultValue($default_class)
                ->end()
                ->append($this->getArgumentsNode())
            ->end();

        return $rootNode;
    }

    /**
     * Arguments node definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function getArgumentsNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('arguments');

        $rootNode
                ->useAttributeAsKey('name')
                ->prototype('variable')
            ->end();

        return $rootNode;
    }

}
