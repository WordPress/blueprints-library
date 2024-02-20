<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Options65d4b00f3beeb
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Options65d4b00f3beeb implements JSONModelInterface
{
    

    
        /** @var bool|null Whether to activate the plugin after installing it. */
        protected $activate;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Options65d4b00f3beeb constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processActivate($rawModelDataInput);
            
        

        
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
   'activate',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Options65d4b00f3beeb',
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
             * Get the value of activate.
             *
             * Whether to activate the plugin after installing it.
             *
             * @return bool|null
             */
            public function getActivate()
                : ?bool
            {
                

                return $this->activate;
            }

            
                /**
                 * Set the value of activate.
                 *
                 * @param bool $activate Whether to activate the plugin after installing it.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setActivate(
                    bool $activate
                ): self {
                    if ($this->activate === $activate) {
                        return $this;
                    }

                    $value = $modelData['activate'] = $activate;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateActivate($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->activate = $value;
                    $this->_rawModelDataInput['activate'] = $activate;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property activate
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processActivate(array $modelData): void
            {
                
                    
                        if (!array_key_exists('activate', $modelData) && $this->activate === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('activate', $modelData) ? $modelData['activate'] : $this->activate;

                

                $this->activate = $this->validateActivate($value, $modelData);
            }

            /**
             * Execute all validators for the property activate
             */
            protected function validateActivate($value, array $modelData)
            {
                
                    

if (!is_bool($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'activate',
  1 => 'bool',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
