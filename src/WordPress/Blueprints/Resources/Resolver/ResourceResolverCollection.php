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
			$resource = $handler->parseUrl($url);
            if ($resource) {
                return $resource;
            }
        }

        return null;
    }

    public function supports(ResourceDefinitionInterface $resource): bool
    {
        foreach ($this->ResourceResolvers as $handler) {
            if ($handler->supports($resource)) {
                return true;
            }
        }

        return false;
    }

    public function stream(ResourceDefinitionInterface $resource)
    {
        foreach ($this->ResourceResolvers as $handler) {
            if ($handler->supports($resource)) {
                return $handler->stream($resource);
            }
        }

        throw new \InvalidArgumentException('Resource ' . get_class($resource) . ' unsupported');
    }
}
