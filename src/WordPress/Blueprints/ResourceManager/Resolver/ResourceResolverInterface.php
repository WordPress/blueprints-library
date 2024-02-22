<?php

namespace WordPress\Blueprints\ResourceManager\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;

interface ResourceResolverInterface
{
    public function parseUrl(string $url): ?ResourceDefinitionInterface;

    public function supports(ResourceDefinitionInterface $resourceDefinition): bool;

    public static function getResourceClass(): string;

    public function stream(ResourceDefinitionInterface $resourceDefinition);
}
