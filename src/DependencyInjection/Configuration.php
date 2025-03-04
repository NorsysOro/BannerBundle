<?php
/**
 * @author Nicolas VERBEKE @ Norsys <nverbeke@norsys.fr>
 */
declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_NODE = 'norsys_banner';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE);

        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'banner_color' => ['value' => 'rgb(0, 105, 53)'],
            ]
        );

        return $treeBuilder;
    }
}
