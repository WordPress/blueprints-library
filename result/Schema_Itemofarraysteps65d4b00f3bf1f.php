<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarraysteps65d4b00f3bf1f
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarraysteps65d4b00f3bf1f implements JSONModelInterface
{
    

    
        /** @var Schema_Progress65d4b00f3bc38|null */
        protected $progress;
    
        /** @var bool|null */
        protected $continueOnError;
    
        /** @var string The step identifier. */
        protected $step;
    
        /** @var string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null */
        protected $themeZipFile;
    
        /** @var Schema_Options65d4b00f3bf49|null Optional installation options. */
        protected $options;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarraysteps65d4b00f3bf1f constructor.
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
            
        
            
                $this->processThemeZipFile($rawModelDataInput);
            
        
            
                $this->processOptions($rawModelDataInput);
            
        

        
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
   'themeZipFile',
   'options',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3bf1f',
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
             * The step identifier.
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
                 * @param string $step The step identifier.
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
                
                    

if ($value !== 'installTheme') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'step',
  1 => 'installTheme',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of themeZipFile.
             *
             * 
             *
             * @return string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null
             */
            public function getThemeZipFile()
                
            {
                

                return $this->themeZipFile;
            }

            
                /**
                 * Set the value of themeZipFile.
                 *
                 * @param string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c $themeZipFile
                 *
                 * 
                 *
                 * @return self
                 */
                public function setThemeZipFile(
                     $themeZipFile
                ): self {
                    if ($this->themeZipFile === $themeZipFile) {
                        return $this;
                    }

                    $value = $modelData['themeZipFile'] = $themeZipFile;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateThemeZipFile($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->themeZipFile = $value;
                    $this->_rawModelDataInput['themeZipFile'] = $themeZipFile;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property themeZipFile
             *
             * @param array $modelData
             *
             * 
             */
            protected function processThemeZipFile(array $modelData): void
            {
                
                    
                        if (!array_key_exists('themeZipFile', $modelData) && $this->themeZipFile === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('themeZipFile', $modelData) ? $modelData['themeZipFile'] : $this->themeZipFile;

                

                $this->themeZipFile = $this->validateThemeZipFile($value, $modelData);
            }

            /**
             * Execute all validators for the property themeZipFile
             */
            protected function validateThemeZipFile($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of options.
             *
             * Optional installation options.
             *
             * @return Schema_Options65d4b00f3bf49|null
             */
            public function getOptions()
                : ?Schema_Options65d4b00f3bf49
            {
                

                return $this->options;
            }

            
                /**
                 * Set the value of options.
                 *
                 * @param Schema_Options65d4b00f3bf49 $options Optional installation options.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setOptions(
                    Schema_Options65d4b00f3bf49 $options
                ): self {
                    if ($this->options === $options) {
                        return $this;
                    }

                    $value = $modelData['options'] = $options;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateOptions($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->options = $value;
                    $this->_rawModelDataInput['options'] = $options;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property options
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processOptions(array $modelData): void
            {
                
                    
                        if (!array_key_exists('options', $modelData) && $this->options === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('options', $modelData) ? $modelData['options'] : $this->options;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Options65d4b00f3bf49($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'options',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->options = $this->validateOptions($value, $modelData);
            }

            /**
             * Execute all validators for the property options
             */
            protected function validateOptions($value, array $modelData)
            {
                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'options',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Options65d4b00f3bf49)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'options',
  1 => 'Schema_Options65d4b00f3bf49',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
