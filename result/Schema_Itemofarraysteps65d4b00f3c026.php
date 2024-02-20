<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarraysteps65d4b00f3c026
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarraysteps65d4b00f3c026 implements JSONModelInterface
{
    

    
        /** @var Schema_Progress65d4b00f3bc38|null */
        protected $progress;
    
        /** @var bool|null */
        protected $continueOnError;
    
        /** @var string */
        protected $step;
    
        /** @var string The path to remove */
        protected $path;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarraysteps65d4b00f3c026 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processProgress($rawModelDataInput);
            
        
            
                $this->processContinueOnError($rawModelDataInput);
            
        
            
                $this->processStep($rawModelDataInput);
            
        
            
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
   'progress',
   'continueOnError',
   'step',
   'path',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c026',
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
             * Get the value of progress.
             *
             * 
             *
             * @return Schema_Progress65d4b00f3bc38|null
             */
            public function getProgress()
                : ?Schema_Progress65d4b00f3bc38
            {
                

                return $this->progress;
            }

            
                /**
                 * Set the value of progress.
                 *
                 * @param Schema_Progress65d4b00f3bc38|null $progress
                 *
                 * 
                 *
                 * @return self
                 */
                public function setProgress(
                    ?Schema_Progress65d4b00f3bc38 $progress
                ): self {
                    if ($this->progress === $progress) {
                        return $this;
                    }

                    $value = $modelData['progress'] = $progress;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateProgress($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->progress = $value;
                    $this->_rawModelDataInput['progress'] = $progress;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property progress
             *
             * @param array $modelData
             *
             * 
             */
            protected function processProgress(array $modelData): void
            {
                
                    
                        if (!array_key_exists('progress', $modelData) && $this->progress === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('progress', $modelData) ? $modelData['progress'] : $this->progress;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Progress65d4b00f3bc38($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'progress',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->progress = $this->validateProgress($value, $modelData);
            }

            /**
             * Execute all validators for the property progress
             */
            protected function validateProgress($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of continueOnError.
             *
             * 
             *
             * @return bool|null
             */
            public function getContinueOnError()
                : ?bool
            {
                

                return $this->continueOnError;
            }

            
                /**
                 * Set the value of continueOnError.
                 *
                 * @param bool $continueOnError
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setContinueOnError(
                    bool $continueOnError
                ): self {
                    if ($this->continueOnError === $continueOnError) {
                        return $this;
                    }

                    $value = $modelData['continueOnError'] = $continueOnError;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateContinueOnError($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->continueOnError = $value;
                    $this->_rawModelDataInput['continueOnError'] = $continueOnError;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property continueOnError
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processContinueOnError(array $modelData): void
            {
                
                    
                        if (!array_key_exists('continueOnError', $modelData) && $this->continueOnError === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('continueOnError', $modelData) ? $modelData['continueOnError'] : $this->continueOnError;

                

                $this->continueOnError = $this->validateContinueOnError($value, $modelData);
            }

            /**
             * Execute all validators for the property continueOnError
             */
            protected function validateContinueOnError($value, array $modelData)
            {
                
                    

if (!is_bool($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'continueOnError',
  1 => 'bool',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of step.
             *
             * 
             *
             * @return string
             */
            public function getStep()
                : string
            {
                

                return $this->step;
            }

            
                /**
                 * Set the value of step.
                 *
                 * @param string $step
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setStep(
                    string $step
                ): self {
                    if ($this->step === $step) {
                        return $this;
                    }

                    $value = $modelData['step'] = $step;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateStep($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->step = $value;
                    $this->_rawModelDataInput['step'] = $step;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property step
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processStep(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('step', $modelData) ? $modelData['step'] : $this->step;

                

                $this->step = $this->validateStep($value, $modelData);
            }

            /**
             * Execute all validators for the property step
             */
            protected function validateStep($value, array $modelData)
            {
                
                    

if ($value !== 'rmdir') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'step',
  1 => 'rmdir',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of path.
             *
             * The path to remove
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
                 * @param string $path The path to remove
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
