<?php

namespace GitHub\DependencyInjection;

use Github\Client;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $authenticationMethods = [
            Client::AUTH_HTTP_TOKEN, Client::AUTH_URL_TOKEN,
            Client::AUTH_HTTP_PASSWORD, Client::AUTH_URL_CLIENT_ID,
        ];

        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('github');
        $rootNode
            ->children()
                ->arrayNode('authentication')
                    ->children()
                        ->scalarNode('token_or_login')
                            ->info('GitHub private token/username/client ID')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('password_or_secret')
                            ->info('GitHub password/secret (optionally can contain authentication nethod)')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('method')
                            ->info('One of the following values: ' . implode(', ', $authenticationMethods))
                            ->defaultNull()
                            ->validate()
                            ->ifNotInArray($authenticationMethods)
                                ->thenInvalid('Invalid authentication method specified %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
