<?php

namespace WordPress\Blueprints\Resources;

use ArrayAccess;
use Symfony\Component\Filesystem\Filesystem;

class ResourceMap implements ArrayAccess
{
    private array $pairs = [];
    private Filesystem $fs;

    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    public function offsetExists($offset): bool
    {
        foreach ($this->pairs as $pair) {
            if ($pair[0] === $offset) {
                return true;
            }
        }
        return false;
    }

    public function offsetGet($offset)
    {
        foreach ($this->pairs as $pair) {
            if ($pair[0] === $offset) {
                return $pair[1];
            }
        }

        // TODO Evaluate waring: 'ext-json' is missing in composer.json
        throw new \Exception("Stream for resource " . json_encode($offset) . " not found");
    }

    public function offsetSet($offset, $value): void
    {
        foreach ($this->pairs as $k => $pair) {
            if ($pair[0] === $offset) {
                $this->pairs[ $k ] = [ $offset, $value ];

                return;
            }
        }
        $this->pairs[] = [ $offset, $value ];
    }

    public function offsetUnset($offset): void
    {
        foreach ($this->pairs as $i => $pair) {
            if ($pair[0] === $offset) {
                unset($this->pairs[ $i ]);
            }
        }
    }
}
