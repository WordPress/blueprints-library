<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Options65d4b00f3c0b3
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Options65d4b00f3c0b3 implements JSONModelInterface
{
    

    
        /** @var string|null */
        protected $adminUsername;
    
        /** @var string|null */
        protected $adminPassword;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Options65d4b00f3c0b3 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processAdminUsername($rawModelDataInput);
            
        
            
                $this->processAdminPassword($rawModelDataInput);
            
        

        
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
   'adminUsername',
   'adminPassword',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Options65d4b00f3c0b3',
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
             * Get the value of adminUsername.
             *
             * 
             *
             * @return string|null
             */
            public function getAdminUsername()
                : ?string
            {
                

                return $this->adminUsername;
            }

            
                /**
                 * Set the value of adminUsername.
                 *
                 * @param string $adminUsername
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setAdminUsername(
                    string $adminUsername
                ): self {
                    if ($this->adminUsername === $adminUsername) {
                        return $this;
                    }

                    $value = $modelData['adminUsername'] = $adminUsername;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateAdminUsername($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->adminUsername = $value;
                    $this->_rawModelDataInput['adminUsername'] = $adminUsername;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property adminUsername
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processAdminUsername(array $modelData): void
            {
                
                    
                        if (!array_key_exists('adminUsername', $modelData) && $this->adminUsername === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('adminUsername', $modelData) ? $modelData['adminUsername'] : $this->adminUsername;

                

                $this->adminUsername = $this->validateAdminUsername($value, $modelData);
            }

            /**
             * Execute all validators for the property adminUsername
             */
            protected function validateAdminUsername($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'adminUsername',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of adminPassword.
             *
             * 
             *
             * @return string|null
             */
            public function getAdminPassword()
                : ?string
            {
                

                return $this->adminPassword;
            }

            
                /**
                 * Set the value of adminPassword.
                 *
                 * @param string $adminPassword
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setAdminPassword(
                    string $adminPassword
                ): self {
                    if ($this->adminPassword === $adminPassword) {
                        return $this;
                    }

                    $value = $modelData['adminPassword'] = $adminPassword;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateAdminPassword($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->adminPassword = $value;
                    $this->_rawModelDataInput['adminPassword'] = $adminPassword;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property adminPassword
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processAdminPassword(array $modelData): void
            {
                
                    
                        if (!array_key_exists('adminPassword', $modelData) && $this->adminPassword === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('adminPassword', $modelData) ? $modelData['adminPassword'] : $this->adminPassword;

                

                $this->adminPassword = $this->validateAdminPassword($value, $modelData);
            }

            /**
             * Execute all validators for the property adminPassword
             */
            protected function validateAdminPassword($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'adminPassword',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
