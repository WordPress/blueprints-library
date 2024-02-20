<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarraysteps65d4b00f3c166
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarraysteps65d4b00f3c166 implements JSONModelInterface
{
    

    
        /** @var Schema_Progress65d4b00f3bc38|null */
        protected $progress;
    
        /** @var bool|null */
        protected $continueOnError;
    
        /** @var string */
        protected $step;
    
        /** @var string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null */
        protected $zipFile;
    
        /** @var string|null The path of the zip file to extract */
        protected $zipPath;
    
        /** @var string The path to extract the zip file to */
        protected $extractToPath;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarraysteps65d4b00f3c166 constructor.
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
            
        
            
                $this->processZipFile($rawModelDataInput);
            
        
            
                $this->processZipPath($rawModelDataInput);
            
        
            
                $this->processExtractToPath($rawModelDataInput);
            
        

        
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
   'zipFile',
   'zipPath',
   'extractToPath',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c166',
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
                
                    

if ($value !== 'unzip') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'step',
  1 => 'unzip',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of zipFile.
             *
             * 
             *
             * @return string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null
             */
            public function getZipFile()
                
            {
                

                return $this->zipFile;
            }

            
                /**
                 * Set the value of zipFile.
                 *
                 * @param string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c $zipFile
                 *
                 * 
                 *
                 * @return self
                 */
                public function setZipFile(
                     $zipFile
                ): self {
                    if ($this->zipFile === $zipFile) {
                        return $this;
                    }

                    $value = $modelData['zipFile'] = $zipFile;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateZipFile($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->zipFile = $value;
                    $this->_rawModelDataInput['zipFile'] = $zipFile;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property zipFile
             *
             * @param array $modelData
             *
             * 
             */
            protected function processZipFile(array $modelData): void
            {
                
                    
                        if (!array_key_exists('zipFile', $modelData) && $this->zipFile === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('zipFile', $modelData) ? $modelData['zipFile'] : $this->zipFile;

                

                $this->zipFile = $this->validateZipFile($value, $modelData);
            }

            /**
             * Execute all validators for the property zipFile
             */
            protected function validateZipFile($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of zipPath.
             *
             * The path of the zip file to extract
             *
             * @return string|null
             */
            public function getZipPath()
                : ?string
            {
                

                return $this->zipPath;
            }

            
                /**
                 * Set the value of zipPath.
                 *
                 * @param string $zipPath The path of the zip file to extract
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setZipPath(
                    string $zipPath
                ): self {
                    if ($this->zipPath === $zipPath) {
                        return $this;
                    }

                    $value = $modelData['zipPath'] = $zipPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateZipPath($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->zipPath = $value;
                    $this->_rawModelDataInput['zipPath'] = $zipPath;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property zipPath
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processZipPath(array $modelData): void
            {
                
                    
                        if (!array_key_exists('zipPath', $modelData) && $this->zipPath === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('zipPath', $modelData) ? $modelData['zipPath'] : $this->zipPath;

                

                $this->zipPath = $this->validateZipPath($value, $modelData);
            }

            /**
             * Execute all validators for the property zipPath
             */
            protected function validateZipPath($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'zipPath',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of extractToPath.
             *
             * The path to extract the zip file to
             *
             * @return string
             */
            public function getExtractToPath()
                : string
            {
                

                return $this->extractToPath;
            }

            
                /**
                 * Set the value of extractToPath.
                 *
                 * @param string $extractToPath The path to extract the zip file to
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setExtractToPath(
                    string $extractToPath
                ): self {
                    if ($this->extractToPath === $extractToPath) {
                        return $this;
                    }

                    $value = $modelData['extractToPath'] = $extractToPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateExtractToPath($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->extractToPath = $value;
                    $this->_rawModelDataInput['extractToPath'] = $extractToPath;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property extractToPath
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processExtractToPath(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('extractToPath', $modelData) ? $modelData['extractToPath'] : $this->extractToPath;

                

                $this->extractToPath = $this->validateExtractToPath($value, $modelData);
            }

            /**
             * Execute all validators for the property extractToPath
             */
            protected function validateExtractToPath($value, array $modelData)
            {
                
                    

if (!array_key_exists('extractToPath', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'extractToPath',
)));
}

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'extractToPath',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
