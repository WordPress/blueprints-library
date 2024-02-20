<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_PreferredVersions65d4b00f3b0f0
 * @package MyApp\Model 
 *
 * The preferred PHP and WordPress versions to use.
 *
 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_PreferredVersions65d4b00f3b0f0 implements JSONModelInterface
{
    

    
        /** @var string The preferred PHP version to use. If not specified, the latest supported version will be used */
        protected $php;
    
        /** @var string The preferred WordPress version to use. If not specified, the latest supported version will be used */
        protected $wp;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_PreferredVersions65d4b00f3b0f0 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processPhp($rawModelDataInput);
            
        
            
                $this->processWp($rawModelDataInput);
            
        

        
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
   'php',
   'wp',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_PreferredVersions65d4b00f3b0f0',
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
             * Get the value of php.
             *
             * The preferred PHP version to use. If not specified, the latest supported version will be used
             *
             * @return string
             */
            public function getPhp()
                : string
            {
                

                return $this->php;
            }

            
                /**
                 * Set the value of php.
                 *
                 * @param string $php The preferred PHP version to use. If not specified, the latest supported version will be used
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setPhp(
                    string $php
                ): self {
                    if ($this->php === $php) {
                        return $this;
                    }

                    $value = $modelData['php'] = $php;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validatePhp($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->php = $value;
                    $this->_rawModelDataInput['php'] = $php;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property php
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processPhp(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('php', $modelData) ? $modelData['php'] : $this->php;

                

                $this->php = $this->validatePhp($value, $modelData);
            }

            /**
             * Execute all validators for the property php
             */
            protected function validatePhp($value, array $modelData)
            {
                
                    

if (!array_key_exists('php', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'php',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'php',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of wp.
             *
             * The preferred WordPress version to use. If not specified, the latest supported version will be used
             *
             * @return string
             */
            public function getWp()
                : string
            {
                

                return $this->wp;
            }

            
                /**
                 * Set the value of wp.
                 *
                 * @param string $wp The preferred WordPress version to use. If not specified, the latest supported version will be used
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setWp(
                    string $wp
                ): self {
                    if ($this->wp === $wp) {
                        return $this;
                    }

                    $value = $modelData['wp'] = $wp;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateWp($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->wp = $value;
                    $this->_rawModelDataInput['wp'] = $wp;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property wp
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processWp(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('wp', $modelData) ? $modelData['wp'] : $this->wp;

                

                $this->wp = $this->validateWp($value, $modelData);
            }

            /**
             * Execute all validators for the property wp
             */
            protected function validateWp($value, array $modelData)
            {
                
                    

if (!array_key_exists('wp', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'wp',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'wp',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
