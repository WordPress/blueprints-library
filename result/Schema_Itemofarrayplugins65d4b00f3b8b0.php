<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarrayplugins65d4b00f3b8b0
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarrayplugins65d4b00f3b8b0 implements JSONModelInterface
{
    

    
        /** @var string Identifies the file resource as Virtual File System (VFS) */
        protected $resource;
    
        /** @var string The path to the file in the VFS */
        protected $path;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarrayplugins65d4b00f3b8b0 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processResource($rawModelDataInput);
            
        
            
                $this->processPath($rawModelDataInput);
            
        

        
            if (count($this->_errorRegistry->getErrors())) {
                throw $this->_errorRegistry;
            }
        

        $this->_rawModelDataInput = $rawModelDataInput;

        
    }

    
        protected function executeBaseValidators(array &$modelData): void
        {
            $value = &$modelData;

            
                

if ($additionalProperties =  (static function () use ($modelData): array {
    $additionalProperties = array_diff(array_keys($modelData), array (
   'resource',
   'path',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarrayplugins65d4b00f3b8b0',
  1 => $additionalProperties,
)));
}

            

            
        }
    

    /**
     * Get the raw input used to set up the model
     *
     * @return array
     */
    public function getRawModelDataInput(): array
    {
        return $this->_rawModelDataInput;
    }

    
        
            /**
             * Get the value of resource.
             *
             * Identifies the file resource as Virtual File System (VFS)
             *
             * @return string
             */
            public function getResource()
                : string
            {
                

                return $this->resource;
            }

            
                /**
                 * Set the value of resource.
                 *
                 * @param string $resource Identifies the file resource as Virtual File System (VFS)
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setResource(
                    string $resource
                ): self {
                    if ($this->resource === $resource) {
                        return $this;
                    }

                    $value = $modelData['resource'] = $resource;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateResource($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->resource = $value;
                    $this->_rawModelDataInput['resource'] = $resource;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property resource
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processResource(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('resource', $modelData) ? $modelData['resource'] : $this->resource;

                

                $this->resource = $this->validateResource($value, $modelData);
            }

            /**
             * Execute all validators for the property resource
             */
            protected function validateResource($value, array $modelData)
            {
                
                    

if ($value !== 'filesystem') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'resource',
  1 => 'filesystem',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of path.
             *
             * The path to the file in the VFS
             *
             * @return string
             */
            public function getPath()
                : string
            {
                

                return $this->path;
            }

            
                /**
                 * Set the value of path.
                 *
                 * @param string $path The path to the file in the VFS
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setPath(
                    string $path
                ): self {
                    if ($this->path === $path) {
                        return $this;
                    }

                    $value = $modelData['path'] = $path;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validatePath($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->path = $value;
                    $this->_rawModelDataInput['path'] = $path;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property path
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processPath(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('path', $modelData) ? $modelData['path'] : $this->path;

                

                $this->path = $this->validatePath($value, $modelData);
            }

            /**
             * Execute all validators for the property path
             */
            protected function validatePath($value, array $modelData)
            {
                
                    

if (!array_key_exists('path', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'path',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'path',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
