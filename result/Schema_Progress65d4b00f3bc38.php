<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Progress65d4b00f3bc38
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Progress65d4b00f3bc38 implements JSONModelInterface
{
    

    
        /** @var float|null */
        protected $weight;
    
        /** @var string|null */
        protected $caption;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Progress65d4b00f3bc38 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processWeight($rawModelDataInput);
            
        
            
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
   'weight',
   'caption',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Progress65d4b00f3bc38',
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
             * Get the value of weight.
             *
             * 
             *
             * @return float|null
             */
            public function getWeight()
                : ?float
            {
                

                return $this->weight;
            }

            
                /**
                 * Set the value of weight.
                 *
                 * @param float $weight
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setWeight(
                    float $weight
                ): self {
                    if ($this->weight === $weight) {
                        return $this;
                    }

                    $value = $modelData['weight'] = $weight;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateWeight($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->weight = $value;
                    $this->_rawModelDataInput['weight'] = $weight;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property weight
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processWeight(array $modelData): void
            {
                
                    
                        if (!array_key_exists('weight', $modelData) && $this->weight === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('weight', $modelData) ? $modelData['weight'] : $this->weight;

                $value = is_int($value) ? (float) $value : $value;

                $this->weight = $this->validateWeight($value, $modelData);
            }

            /**
             * Execute all validators for the property weight
             */
            protected function validateWeight($value, array $modelData)
            {
                
                    

if (!is_float($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'weight',
  1 => 'float',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of caption.
             *
             * 
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
                 * @param string $caption
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
