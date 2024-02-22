<?php

namespace WordPress\Blueprints\ResourceManager\Resolver;

use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\InlineResource;

class InlineResourceResolver implements ResourceResolverInterface
{

    public function parseUrl(string $url): ?ResourceDefinitionInterface
    {
        // If url starts with "protocol://" then we assume it's not inline raw data
        if (0 !== preg_match('#^[a-z_+]+://#', $url)) {
            return null;
        }

        return ( new InlineResource() )->setContents($url);
    }

    public static function getResourceClass(): string
    {
        return InlineResource::class;
    }

    public function supports(ResourceDefinitionInterface $resourceDefinition): bool
    {
        return $resourceDefinition instanceof InlineResource;
    }

    public function stream(ResourceDefinitionInterface $resourceDefinition)
    {
        if (! $this->supports($resourceDefinition)) {
            throw new \InvalidArgumentException('Resource ' . get_class($resourceDefinition) . ' unsupported');
        }
        /** @var $resourceDefinition InlineResource */
        $fp = fopen("php://temp", 'r+');
        fwrite($fp, $resourceDefinition->contents);
        rewind($fp);

        return $fp;
    }
}
