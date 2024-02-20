<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Features65d4b00f3b44e
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Features65d4b00f3b44e implements JSONModelInterface
{
    

    
        /** @var bool|null Should boot with support for network request via wp_safe_remote_get? */
        protected $networking;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Features65d4b00f3b44e constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processNetworking($rawModelDataInput);
            
        

        
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
   'networking',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Features65d4b00f3b44e',
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
             * Get the value of networking.
             *
             * Should boot with support for network request via wp_safe_remote_get?
             *
             * @return bool|null
             */
            public function getNetworking()
                : ?bool
            {
                

                return $this->networking;
            }

            
                /**
                 * Set the value of networking.
                 *
                 * @param bool $networking Should boot with support for network request via wp_safe_remote_get?
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setNetworking(
                    bool $networking
                ): self {
                    if ($this->networking === $networking) {
                        return $this;
                    }

                    $value = $modelData['networking'] = $networking;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateNetworking($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->networking = $value;
                    $this->_rawModelDataInput['networking'] = $networking;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property networking
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processNetworking(array $modelData): void
            {
                
                    
                        if (!array_key_exists('networking', $modelData) && $this->networking === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('networking', $modelData) ? $modelData['networking'] : $this->networking;

                

                $this->networking = $this->validateNetworking($value, $modelData);
            }

            /**
             * Execute all validators for the property networking
             */
            protected function validateNetworking($value, array $modelData)
            {
                
                    

if (!is_bool($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'networking',
  1 => 'bool',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
