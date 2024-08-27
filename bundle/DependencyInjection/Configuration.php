<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private string $rootNodeName;

    public function __construct(string $rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->rootNodeName);
        $rootNode = $treeBuilder->getRootNode();

        $this->addShowExternalSiteaccessUrls($rootNode);

        return $treeBuilder;
    }

    private function addShowExternalSiteaccessUrls(ArrayNodeDefinition $nodeDefinition): void
    {
        $nodeDefinition
            ->treatFalseLike(['enabled' => false])
            ->treatTrueLike(['enabled' => true])
            ->treatNullLike(['enabled' => false])
                ->children()
                    ->booleanNode('show_siteaccess_urls_outside_configured_content_tree_root')
                        ->defaultFalse()
                        ->info("Show Siteaccess URLs outside the configured Content tree root in administration's URL tab")
                    ->end()
                ?->end();
    }
}
