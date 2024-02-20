<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_SiteOptions65d4b00f3bb76
 * @package MyApp\Model 
 *
 * WordPress site options to define
 *
 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_SiteOptions65d4b00f3bb76 implements JSONModelInterface
{
    

    
        /** @var string|null The site title */
        protected $blogname;
    
        /** @var string[] Collect all additional properties provided to the schema */
        private $_additionalProperties = array (
);
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_SiteOptions65d4b00f3bb76 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processBlogname($rawModelDataInput);
            
        
            
        

        
            if (count($this->_errorRegistry->getErrors())) {
                throw $this->_errorRegistry;
            }
        

        $this->_rawModelDataInput = $rawModelDataInput;

        
    }

    
        protected function executeBaseValidators(array &$modelData): void
        {
            $value = &$modelData;

            
                

            $properties = $value;
            $invalidProperties = [];
        
if ((function () use ($properties, &$invalidProperties) {
    
        $originalErrorRegistry = $this->_errorRegistry;
    
    
        $rollbackValues = $this->_additionalProperties;
    

    foreach (array_diff(array_keys($properties), array (
   'blogname',
)) as $propertyKey) {
        

        try {
            $value = $properties[$propertyKey];

            
                $this->_errorRegistry = new ErrorRegistryException();
            

            

            
                

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'additional property',
  1 => 'string',
)));
}

            

            
                if ($this->_errorRegistry->getErrors()) {
                    $invalidProperties[$propertyKey] = $this->_errorRegistry->getErrors();
                }
            

            
                $this->_additionalProperties[$propertyKey] = $value;
            
        } catch (\Exception $e) {
            // collect all errors concerning invalid additional properties
            isset($invalidProperties[$propertyKey])
                ? $invalidProperties[$propertyKey][] = $e
                : $invalidProperties[$propertyKey] = [$e];
        }
    }

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    
        if (!empty($invalidProperties)) {
            $this->_additionalProperties = $rollbackValues;
        }
    

    return !empty($invalidProperties);
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidAdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_SiteOptions65d4b00f3bb76',
  1 => $invalidProperties,
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
             * Get the value of blogname.
             *
             * The site title
             *
             * @return string|null
             */
            public function getBlogname()
                : ?string
            {
                

                return $this->blogname;
            }

            
                /**
                 * Set the value of blogname.
                 *
                 * @param string $blogname The site title
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setBlogname(
                    string $blogname
                ): self {
                    if ($this->blogname === $blogname) {
                        return $this;
                    }

                    $value = $modelData['blogname'] = $blogname;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateBlogname($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->blogname = $value;
                    $this->_rawModelDataInput['blogname'] = $blogname;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property blogname
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processBlogname(array $modelData): void
            {
                
                    
                        if (!array_key_exists('blogname', $modelData) && $this->blogname === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('blogname', $modelData) ? $modelData['blogname'] : $this->blogname;

                

                $this->blogname = $this->validateBlogname($value, $modelData);
            }

            /**
             * Execute all validators for the property blogname
             */
            protected function validateBlogname($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'blogname',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
    

    
}

// @codeCoverageIgnoreEnd
