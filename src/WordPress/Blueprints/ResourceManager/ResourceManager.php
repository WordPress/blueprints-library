<?php

namespace WordPress\Blueprints\ResourceManager;

use Symfony\Component\Filesystem\Filesystem;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\ResourceManager\Resolver\ResourceResolverInterface;

class ResourceManager
{

    protected Filesystem $fs;
    protected ResourceMap $map;
    protected ResourceResolverInterface $resourceResolver;

    public function __construct(
        ResourceResolverInterface $resourceResolver
    ) {
        $this->resourceResolver = $resourceResolver;
        $this->fs = new Filesystem();
        $this->map = new ResourceMap();
    }

    public function enqueue(array $resourceDeclarations)
    {
        foreach ($resourceDeclarations as [$originalInput, $resource]) {
            $this->map[ $originalInput ] = $this->resourceResolver->stream($resource);
        }
    }

    public function getStream($key)
    {
        return $this->map[ $key ];
    }


    public function bufferToTemporaryFile($resource, $callback, $suffix = null)
    {
        $fp = $this->getStream($resource);
        $path = $this->fs->tempnam(sys_get_temp_dir(), 'resource', $suffix);
        $this->fs->dumpFile($path, $fp);

        try {
            return $callback($path);
        } finally {
            $this->fs->remove($path);
        }
    }
}
