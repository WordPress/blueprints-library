<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;

interface ResourceResolverInterface
{
    public function parseUrl(string $url): ?ResourceDefinitionInterface;

    public function supports(ResourceDefinitionInterface $resource): bool;

    public static function getResourceClass(): string;

    public function stream(ResourceDefinitionInterface $resource);
}
