<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\FilesystemResource;

class FilesystemResourceResolver implements ResourceResolverInterface
{

    public function parseUrl(string $url): ?ResourceDefinitionInterface
    {
        if (! str_starts_with($url, 'file://')) {
            return null;
        }

        $resourceDefinition = new FilesystemResource();
        $resourceDefinition->setPath($url);
        return $resourceDefinition;
    }

    public static function getResourceClass(): string
    {
        return FilesystemResource::class;
    }

    public function supports(ResourceDefinitionInterface $resourceDefinition): bool
    {
        return $resourceDefinition instanceof FilesystemResource;
    }

    public function stream(ResourceDefinitionInterface $resourceDefinition)
    {
        if (! $this->supports($resourceDefinition)) {
            throw new \InvalidArgumentException('Resource ' . get_class($resourceDefinition) . ' unsupported');
        }

        /** @var $resourceDefinition FilesystemResource */
        return fopen($resourceDefinition->path, 'r');
    }
}
