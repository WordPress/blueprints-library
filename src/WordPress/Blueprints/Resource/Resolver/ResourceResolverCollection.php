<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;

class ResourceResolverCollection implements ResourceResolverInterface
{

    /** @var ResourceResolverInterface[] */
    protected array $ResourceResolvers;

    public function __construct(
        $ResourceResolvers
    ) {
        $this->ResourceResolvers = $ResourceResolvers;
    }

    public static function getResourceClass(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function parseUrl(string $url): ?ResourceDefinitionInterface
    {
        foreach ($this->ResourceResolvers as $handler) {
            $resourceFilePath = $handler->parseUrl($url);
            if ($resourceFilePath) {
                return $resourceFilePath;
            }
        }

        return null;
    }

    public function supports(ResourceDefinitionInterface $resourceDefinition): bool
    {
        foreach ($this->ResourceResolvers as $handler) {
            if ($handler->supports($resourceDefinition)) {
                return true;
            }
        }

        return false;
    }

    public function stream(ResourceDefinitionInterface $resourceDefinition)
    {
        foreach ($this->ResourceResolvers as $handler) {
            if ($handler->supports($resourceDefinition)) {
                return $handler->stream($resourceDefinition);
            }
        }

        throw new \InvalidArgumentException('Resource ' . get_class($resourceDefinition) . ' unsupported');
    }
}
