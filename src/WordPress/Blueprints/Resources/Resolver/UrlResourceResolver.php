<?php

namespace WordPress\Blueprints\Resources\Resolver;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\DataSource\DataSourceInterface;

class UrlResourceResolver implements ResourceResolverInterface
{

    protected DataSourceInterface $dataSource;

    public function __construct(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function parseUrl(string $url): ?ResourceDefinitionInterface
    {
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return null;
        }

        return ( new UrlResource() )->setUrl($url);
    }


    public static function getResourceClass(): string
    {
        return UrlResource::class;
    }

    public function supports(ResourceDefinitionInterface $resource): bool
    {
        return $resource instanceof UrlResource;
    }

    public function stream(ResourceDefinitionInterface $resource)
    {
        if (! $this->supports($resource)) {
            throw new \InvalidArgumentException('Resource ' . get_class($resource) . ' unsupported');
        }

        /** @var $resource UrlResource */
        return $this->dataSource->stream($resource->url);
    }
}
