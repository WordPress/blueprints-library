<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarrayplugins65d4b00f3b9ea
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarrayplugins65d4b00f3b9ea implements JSONModelInterface
{
    

    
        /** @var string Identifies the file resource as a URL */
        protected $resource;
    
        /** @var string The URL of the file */
        protected $url;
    
        /** @var string|null Optional caption for displaying a progress message */
        protected $caption;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarrayplugins65d4b00f3b9ea constructor.
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
            
        
            
                $this->processUrl($rawModelDataInput);
            
        
            
                $this->processCaption($rawModelDataInput);
            
        

        
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
   'url',
   'caption',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarrayplugins65d4b00f3b9ea',
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
             * Identifies the file resource as a URL
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
                 * @param string $resource Identifies the file resource as a URL
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
                
                    

if ($value !== 'url') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'resource',
  1 => 'url',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of url.
             *
             * The URL of the file
             *
             * @return string
             */
            public function getUrl()
                : string
            {
                

                return $this->url;
            }

            
                /**
                 * Set the value of url.
                 *
                 * @param string $url The URL of the file
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setUrl(
                    string $url
                ): self {
                    if ($this->url === $url) {
                        return $this;
                    }

                    $value = $modelData['url'] = $url;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateUrl($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->url = $value;
                    $this->_rawModelDataInput['url'] = $url;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property url
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processUrl(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('url', $modelData) ? $modelData['url'] : $this->url;

                

                $this->url = $this->validateUrl($value, $modelData);
            }

            /**
             * Execute all validators for the property url
             */
            protected function validateUrl($value, array $modelData)
            {
                
                    

if (!array_key_exists('url', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'url',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'url',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of caption.
             *
             * Optional caption for displaying a progress message
             *
             * @return string|null
             */
            public function getCaption()
                : ?string
            {
                

                return $this->caption;
            }

            
                /**
                 * Set the value of caption.
                 *
                 * @param string $caption Optional caption for displaying a progress message
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setCaption(
                    string $caption
                ): self {
                    if ($this->caption === $caption) {
                        return $this;
                    }

                    $value = $modelData['caption'] = $caption;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateCaption($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->caption = $value;
                    $this->_rawModelDataInput['caption'] = $caption;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property caption
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processCaption(array $modelData): void
            {
                
                    
                        if (!array_key_exists('caption', $modelData) && $this->caption === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('caption', $modelData) ? $modelData['caption'] : $this->caption;

                

                $this->caption = $this->validateCaption($value, $modelData);
            }

            /**
             * Execute all validators for the property caption
             */
            protected function validateCaption($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'caption',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
