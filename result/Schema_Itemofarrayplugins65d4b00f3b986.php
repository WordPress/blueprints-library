<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarrayplugins65d4b00f3b986
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarrayplugins65d4b00f3b986 implements JSONModelInterface
{
    

    
        /** @var string Identifies the file resource as a WordPress Core theme */
        protected $resource;
    
        /** @var string The slug of the WordPress Core theme */
        protected $slug;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarrayplugins65d4b00f3b986 constructor.
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
            
        
            
                $this->processSlug($rawModelDataInput);
            
        

        
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
   'slug',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarrayplugins65d4b00f3b986',
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
             * Identifies the file resource as a WordPress Core theme
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
                 * @param string $resource Identifies the file resource as a WordPress Core theme
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
                
                    

if ($value !== 'wordpress.org/themes') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'resource',
  1 => 'wordpress.org/themes',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of slug.
             *
             * The slug of the WordPress Core theme
             *
             * @return string
             */
            public function getSlug()
                : string
            {
                

                return $this->slug;
            }

            
                /**
                 * Set the value of slug.
                 *
                 * @param string $slug The slug of the WordPress Core theme
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setSlug(
                    string $slug
                ): self {
                    if ($this->slug === $slug) {
                        return $this;
                    }

                    $value = $modelData['slug'] = $slug;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateSlug($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->slug = $value;
                    $this->_rawModelDataInput['slug'] = $slug;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property slug
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processSlug(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('slug', $modelData) ? $modelData['slug'] : $this->slug;

                

                $this->slug = $this->validateSlug($value, $modelData);
            }

            /**
             * Execute all validators for the property slug
             */
            protected function validateSlug($value, array $modelData)
            {
                
                    

if (!array_key_exists('slug', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'slug',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'slug',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
