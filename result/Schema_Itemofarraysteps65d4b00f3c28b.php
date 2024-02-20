<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Itemofarraysteps65d4b00f3c28b
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Itemofarraysteps65d4b00f3c28b implements JSONModelInterface
{
    

    
        /** @var mixed */
        protected $step;
    
        /** @var Schema_Progress65d4b00f3bc38|null */
        protected $progress;
    
        /** @var bool|null */
        protected $continueOnError;
    
        /** @var string|null Plugin slug, like 'gutenberg' or 'hello-dolly'. */
        protected $slug;
    
        /** @var string|null Source path */
        protected $fromPath;
    
        /** @var string|null Target path */
        protected $toPath;
    
        /** @var Schema_Consts65d4b00f3bde0|null The constants to define */
        protected $consts;
    
        /** @var string|null The URL */
        protected $siteUrl;
    
        /** @var string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null */
        protected $itemOfArrayPlugins;
    
        /** @var Schema_Options65d4b00f3beeb|null */
        protected $options;
    
        /** @var string|null The path of the directory you want to create */
        protected $path;
    
        /** @var string|null The PHP code to run. */
        protected $code;
    
        /** @var string|null The path of the zip file to extract */
        protected $zipPath;
    
        /** @var string|null The path to extract the zip file to */
        protected $extractToPath;
    
        /** @var string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null The data to write */
        protected $data;
    
        /** @var string[]|null The WP CLI command to run. */
        protected $command;
    
        /** @var array Track the internal validation state of composed validations */
        private $_propertyValidationState = array (
  0 => 
  array (
  ),
);
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Itemofarraysteps65d4b00f3c28b constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processStep($rawModelDataInput);
            
        
            
                $this->processProgress($rawModelDataInput);
            
        
            
                $this->processContinueOnError($rawModelDataInput);
            
        
            
                $this->processSlug($rawModelDataInput);
            
        
            
                $this->processFromPath($rawModelDataInput);
            
        
            
                $this->processToPath($rawModelDataInput);
            
        
            
                $this->processConsts($rawModelDataInput);
            
        
            
                $this->processSiteUrl($rawModelDataInput);
            
        
            
                $this->processItemOfArrayPlugins($rawModelDataInput);
            
        
            
                $this->processOptions($rawModelDataInput);
            
        
            
                $this->processPath($rawModelDataInput);
            
        
            
                $this->processCode($rawModelDataInput);
            
        
            
                $this->processZipPath($rawModelDataInput);
            
        
            
                $this->processExtractToPath($rawModelDataInput);
            
        
            
                $this->processData($rawModelDataInput);
            
        
            
                $this->processCommand($rawModelDataInput);
            
        
            
        

        
            if (count($this->_errorRegistry->getErrors())) {
                throw $this->_errorRegistry;
            }
        

        $this->_rawModelDataInput = $rawModelDataInput;

        
    }

    
        protected function executeBaseValidators(array &$modelData): void
        {
            $value = &$modelData;

            

            
                $this->validateComposition_0($modelData);
            
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
             * Get the value of step.
             *
             * 
             *
             * @return mixed
             */
            public function getStep()
                
            {
                

                return $this->step;
            }

            
                /**
                 * Set the value of step.
                 *
                 * @param mixed $step
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setStep(
                     $step
                ): self {
                    if ($this->step === $step) {
                        return $this;
                    }

                    $value = $modelData['step'] = $step;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
                
                    

if (!array_key_exists('step', $modelData)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\RequiredValueException($value ?? null, ...array (
  0 => 'step',
)));
}

                

                return $value;
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
                    

                    $this->validateComposition_0($modelData);



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
                 * @param bool|null $continueOnError
                 *
                 * 
                 *
                 * @return self
                 */
                public function setContinueOnError(
                    ?bool $continueOnError
                ): self {
                    if ($this->continueOnError === $continueOnError) {
                        return $this;
                    }

                    $value = $modelData['continueOnError'] = $continueOnError;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
             * 
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
                

                return $value;
            }
        
    
        
            /**
             * Get the value of slug.
             *
             * Plugin slug, like 'gutenberg' or 'hello-dolly'.
             *
             * @return string|null
             */
            public function getSlug()
                : ?string
            {
                

                return $this->slug;
            }

            
                /**
                 * Set the value of slug.
                 *
                 * @param string|null $slug Plugin slug, like 'gutenberg' or 'hello-dolly'.
                 *
                 * 
                 *
                 * @return self
                 */
                public function setSlug(
                    ?string $slug
                ): self {
                    if ($this->slug === $slug) {
                        return $this;
                    }

                    $value = $modelData['slug'] = $slug;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateSlug($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->slug = $value;
                    $this->_rawModelDataInput['slug'] = $slug;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property slug
             *
             * @param array $modelData
             *
             * 
             */
            protected function processSlug(array $modelData): void
            {
                
                    
                        if (!array_key_exists('slug', $modelData) && $this->slug === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('slug', $modelData) ? $modelData['slug'] : $this->slug;

                

                $this->slug = $this->validateSlug($value, $modelData);
            }

            /**
             * Execute all validators for the property slug
             */
            protected function validateSlug($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of fromPath.
             *
             * Source path
             *
             * @return string|null
             */
            public function getFromPath()
                : ?string
            {
                

                return $this->fromPath;
            }

            
                /**
                 * Set the value of fromPath.
                 *
                 * @param string|null $fromPath Source path
                 *
                 * 
                 *
                 * @return self
                 */
                public function setFromPath(
                    ?string $fromPath
                ): self {
                    if ($this->fromPath === $fromPath) {
                        return $this;
                    }

                    $value = $modelData['fromPath'] = $fromPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateFromPath($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->fromPath = $value;
                    $this->_rawModelDataInput['fromPath'] = $fromPath;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property fromPath
             *
             * @param array $modelData
             *
             * 
             */
            protected function processFromPath(array $modelData): void
            {
                
                    
                        if (!array_key_exists('fromPath', $modelData) && $this->fromPath === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('fromPath', $modelData) ? $modelData['fromPath'] : $this->fromPath;

                

                $this->fromPath = $this->validateFromPath($value, $modelData);
            }

            /**
             * Execute all validators for the property fromPath
             */
            protected function validateFromPath($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of toPath.
             *
             * Target path
             *
             * @return string|null
             */
            public function getToPath()
                : ?string
            {
                

                return $this->toPath;
            }

            
                /**
                 * Set the value of toPath.
                 *
                 * @param string|null $toPath Target path
                 *
                 * 
                 *
                 * @return self
                 */
                public function setToPath(
                    ?string $toPath
                ): self {
                    if ($this->toPath === $toPath) {
                        return $this;
                    }

                    $value = $modelData['toPath'] = $toPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateToPath($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->toPath = $value;
                    $this->_rawModelDataInput['toPath'] = $toPath;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property toPath
             *
             * @param array $modelData
             *
             * 
             */
            protected function processToPath(array $modelData): void
            {
                
                    
                        if (!array_key_exists('toPath', $modelData) && $this->toPath === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('toPath', $modelData) ? $modelData['toPath'] : $this->toPath;

                

                $this->toPath = $this->validateToPath($value, $modelData);
            }

            /**
             * Execute all validators for the property toPath
             */
            protected function validateToPath($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of consts.
             *
             * The constants to define
             *
             * @return Schema_Consts65d4b00f3bde0|null
             */
            public function getConsts()
                : ?Schema_Consts65d4b00f3bde0
            {
                

                return $this->consts;
            }

            
                /**
                 * Set the value of consts.
                 *
                 * @param Schema_Consts65d4b00f3bde0|null $consts The constants to define
                 *
                 * 
                 *
                 * @return self
                 */
                public function setConsts(
                    ?Schema_Consts65d4b00f3bde0 $consts
                ): self {
                    if ($this->consts === $consts) {
                        return $this;
                    }

                    $value = $modelData['consts'] = $consts;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateConsts($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->consts = $value;
                    $this->_rawModelDataInput['consts'] = $consts;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property consts
             *
             * @param array $modelData
             *
             * 
             */
            protected function processConsts(array $modelData): void
            {
                
                    
                        if (!array_key_exists('consts', $modelData) && $this->consts === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('consts', $modelData) ? $modelData['consts'] : $this->consts;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Consts65d4b00f3bde0($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'consts',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->consts = $this->validateConsts($value, $modelData);
            }

            /**
             * Execute all validators for the property consts
             */
            protected function validateConsts($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of siteUrl.
             *
             * The URL
             *
             * @return string|null
             */
            public function getSiteUrl()
                : ?string
            {
                

                return $this->siteUrl;
            }

            
                /**
                 * Set the value of siteUrl.
                 *
                 * @param string|null $siteUrl The URL
                 *
                 * 
                 *
                 * @return self
                 */
                public function setSiteUrl(
                    ?string $siteUrl
                ): self {
                    if ($this->siteUrl === $siteUrl) {
                        return $this;
                    }

                    $value = $modelData['siteUrl'] = $siteUrl;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateSiteUrl($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->siteUrl = $value;
                    $this->_rawModelDataInput['siteUrl'] = $siteUrl;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property siteUrl
             *
             * @param array $modelData
             *
             * 
             */
            protected function processSiteUrl(array $modelData): void
            {
                
                    
                        if (!array_key_exists('siteUrl', $modelData) && $this->siteUrl === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('siteUrl', $modelData) ? $modelData['siteUrl'] : $this->siteUrl;

                

                $this->siteUrl = $this->validateSiteUrl($value, $modelData);
            }

            /**
             * Execute all validators for the property siteUrl
             */
            protected function validateSiteUrl($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of item of array plugins.
             *
             * 
             *
             * @return string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null
             */
            public function getItemOfArrayPlugins()
                
            {
                

                return $this->itemOfArrayPlugins;
            }

            
                /**
                 * Set the value of item of array plugins.
                 *
                 * @param string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c $itemOfArrayPlugins
                 *
                 * 
                 *
                 * @return self
                 */
                public function setItemOfArrayPlugins(
                     $itemOfArrayPlugins
                ): self {
                    if ($this->itemOfArrayPlugins === $itemOfArrayPlugins) {
                        return $this;
                    }

                    $value = $modelData['item of array plugins'] = $itemOfArrayPlugins;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    



                    $value = $this->validateItemOfArrayPlugins($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->itemOfArrayPlugins = $value;
                    $this->_rawModelDataInput['item of array plugins'] = $itemOfArrayPlugins;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property itemOfArrayPlugins
             *
             * @param array $modelData
             *
             * 
             */
            protected function processItemOfArrayPlugins(array $modelData): void
            {
                
                    
                        if (!array_key_exists('item of array plugins', $modelData) && $this->itemOfArrayPlugins === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('item of array plugins', $modelData) ? $modelData['item of array plugins'] : $this->itemOfArrayPlugins;

                

                $this->itemOfArrayPlugins = $this->validateItemOfArrayPlugins($value, $modelData);
            }

            /**
             * Execute all validators for the property itemOfArrayPlugins
             */
            protected function validateItemOfArrayPlugins($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of options.
             *
             * 
             *
             * @return Schema_Options65d4b00f3beeb|null
             */
            public function getOptions()
                : ?Schema_Options65d4b00f3beeb
            {
                

                return $this->options;
            }

            
                /**
                 * Set the value of options.
                 *
                 * @param Schema_Options65d4b00f3beeb|null $options
                 *
                 * 
                 *
                 * @return self
                 */
                public function setOptions(
                    ?Schema_Options65d4b00f3beeb $options
                ): self {
                    if ($this->options === $options) {
                        return $this;
                    }

                    $value = $modelData['options'] = $options;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
             * 
             */
            protected function processOptions(array $modelData): void
            {
                
                    
                        if (!array_key_exists('options', $modelData) && $this->options === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('options', $modelData) ? $modelData['options'] : $this->options;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Options65d4b00f3beeb($value) : $value;
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
                

                return $value;
            }
        
    
        
            /**
             * Get the value of path.
             *
             * The path of the directory you want to create
             *
             * @return string|null
             */
            public function getPath()
                : ?string
            {
                

                return $this->path;
            }

            
                /**
                 * Set the value of path.
                 *
                 * @param string|null $path The path of the directory you want to create
                 *
                 * 
                 *
                 * @return self
                 */
                public function setPath(
                    ?string $path
                ): self {
                    if ($this->path === $path) {
                        return $this;
                    }

                    $value = $modelData['path'] = $path;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
             * 
             */
            protected function processPath(array $modelData): void
            {
                
                    
                        if (!array_key_exists('path', $modelData) && $this->path === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('path', $modelData) ? $modelData['path'] : $this->path;

                

                $this->path = $this->validatePath($value, $modelData);
            }

            /**
             * Execute all validators for the property path
             */
            protected function validatePath($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of code.
             *
             * The PHP code to run.
             *
             * @return string|null
             */
            public function getCode()
                : ?string
            {
                

                return $this->code;
            }

            
                /**
                 * Set the value of code.
                 *
                 * @param string|null $code The PHP code to run.
                 *
                 * 
                 *
                 * @return self
                 */
                public function setCode(
                    ?string $code
                ): self {
                    if ($this->code === $code) {
                        return $this;
                    }

                    $value = $modelData['code'] = $code;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateCode($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->code = $value;
                    $this->_rawModelDataInput['code'] = $code;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property code
             *
             * @param array $modelData
             *
             * 
             */
            protected function processCode(array $modelData): void
            {
                
                    
                        if (!array_key_exists('code', $modelData) && $this->code === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('code', $modelData) ? $modelData['code'] : $this->code;

                

                $this->code = $this->validateCode($value, $modelData);
            }

            /**
             * Execute all validators for the property code
             */
            protected function validateCode($value, array $modelData)
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
                 * @param string|null $zipPath The path of the zip file to extract
                 *
                 * 
                 *
                 * @return self
                 */
                public function setZipPath(
                    ?string $zipPath
                ): self {
                    if ($this->zipPath === $zipPath) {
                        return $this;
                    }

                    $value = $modelData['zipPath'] = $zipPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
             * 
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
                

                return $value;
            }
        
    
        
            /**
             * Get the value of extractToPath.
             *
             * The path to extract the zip file to
             *
             * @return string|null
             */
            public function getExtractToPath()
                : ?string
            {
                

                return $this->extractToPath;
            }

            
                /**
                 * Set the value of extractToPath.
                 *
                 * @param string|null $extractToPath The path to extract the zip file to
                 *
                 * 
                 *
                 * @return self
                 */
                public function setExtractToPath(
                    ?string $extractToPath
                ): self {
                    if ($this->extractToPath === $extractToPath) {
                        return $this;
                    }

                    $value = $modelData['extractToPath'] = $extractToPath;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



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
             * 
             */
            protected function processExtractToPath(array $modelData): void
            {
                
                    
                        if (!array_key_exists('extractToPath', $modelData) && $this->extractToPath === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('extractToPath', $modelData) ? $modelData['extractToPath'] : $this->extractToPath;

                

                $this->extractToPath = $this->validateExtractToPath($value, $modelData);
            }

            /**
             * Execute all validators for the property extractToPath
             */
            protected function validateExtractToPath($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of data.
             *
             * The data to write
             *
             * @return string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null
             */
            public function getData()
                : ?string
            {
                

                return $this->data;
            }

            
                /**
                 * Set the value of data.
                 *
                 * @param string|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c|null $data The data to write
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setData(
                    ?string $data
                ): self {
                    if ($this->data === $data) {
                        return $this;
                    }

                    $value = $modelData['data'] = $data;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateData($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->data = $value;
                    $this->_rawModelDataInput['data'] = $data;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property data
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processData(array $modelData): void
            {
                
                    
                        if (!array_key_exists('data', $modelData) && $this->data === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('data', $modelData) ? $modelData['data'] : $this->data;

                

                $this->data = $this->validateData($value, $modelData);
            }

            /**
             * Execute all validators for the property data
             */
            protected function validateData($value, array $modelData)
            {
                
                    $this->validateData_ComposedProperty_65d4b00f3c1f8($value);
                

                return $value;
            }
        
    
        
            /**
             * Get the value of command.
             *
             * The WP CLI command to run.
             *
             * @return string[]|null
             */
            public function getCommand()
                : ?array
            {
                

                return $this->command;
            }

            
                /**
                 * Set the value of command.
                 *
                 * @param string[]|null $command The WP CLI command to run.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setCommand(
                    ?array $command
                ): self {
                    if ($this->command === $command) {
                        return $this;
                    }

                    $value = $modelData['command'] = $command;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    $this->validateComposition_0($modelData);



                    $value = $this->validateCommand($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->command = $value;
                    $this->_rawModelDataInput['command'] = $command;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property command
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processCommand(array $modelData): void
            {
                
                    
                        if (!array_key_exists('command', $modelData) && $this->command === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('command', $modelData) ? $modelData['command'] : $this->command;

                

                $this->command = $this->validateCommand($value, $modelData);
            }

            /**
             * Execute all validators for the property command
             */
            protected function validateCommand($value, array $modelData)
            {
                
                    $this->validateCommand_ComposedProperty_65d4b00f3c25c($value);
                

                return $value;
            }
        
    
        
    

    /**
 * Validate updated properties which are part of a composition validation
 *
 * @param array $modifiedModelData An array containing all updated data as key-value pairs
 *
 * 
 */
private function validateComposition_0(array &$modifiedModelData): void
{
    $validatorIndex = 0;
    $value = $modelData = array_merge($this->_rawModelDataInput, $modifiedModelData);

    
            $succeededCompositionElements = 0;
            $compositionErrorCollection = [];
        
    if (
(function (&$value) use (
    &$modelData,
    &$modifiedModelData,
    &$compositionErrorCollection,
    &$succeededCompositionElements,
    &$validatorIndex
) {
    $succeededCompositionElements = 20;
    $validatorComponentIndex = 0;
    $originalModelData = $value;
    $proposedValue = null;
    $modifiedValues = [];

    
        $originalPropertyValidationState = $this->_propertyValidationState ?? [];
    

    
        $originalErrorRegistry = $this->_errorRegistry;
    

    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'slug',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bc1a($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bc1a)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bc1a',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['slug'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'slug',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bd2b($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bd2b)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bd2b',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['slug'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'fromPath',
                            
                                'toPath',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bd71($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bd71)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bd71',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['fromPath'] = null;
            
                $modelData['toPath'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'consts',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bdb9($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bdb9)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bdb9',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['consts'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'siteUrl',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3be19($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3be19)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3be19',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['siteUrl'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3be55($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3be55)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3be55',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'file',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3be88($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3be88)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3be88',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['file'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'pluginZipFile',
                            
                                'options',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bec0($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bec0)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bec0',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['pluginZipFile'] = null;
            
                $modelData['options'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'themeZipFile',
                            
                                'options',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bf1f($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bf1f)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bf1f',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['themeZipFile'] = null;
            
                $modelData['options'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'path',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bf77($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bf77)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bf77',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['path'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'fromPath',
                            
                                'toPath',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bfac($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bfac)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bfac',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['fromPath'] = null;
            
                $modelData['toPath'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'path',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3bfed($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3bfed)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3bfed',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['path'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'path',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c026($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c026)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c026',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['path'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'code',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c05a($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c05a)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c05a',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['code'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'options',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c092($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c092)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c092',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['options'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'sql',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c0e9($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c0e9)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c0e9',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['sql'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'options',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c123($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c123)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c123',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['options'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'zipFile',
                            
                                'zipPath',
                            
                                'extractToPath',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c166($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c166)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c166',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['zipFile'] = null;
            
                $modelData['zipPath'] = null;
            
                $modelData['extractToPath'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'path',
                            
                                'data',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c1ae($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c1ae)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c1ae',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['path'] = null;
            
                $modelData['data'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            
                // check if the state of the validator is already known.
                // If none of the properties affected by the validator are changed the validator must not be re-evaluated
                if (isset($validatorIndex) &&
                    isset($this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]) &&
                    !array_intersect(
                        array_keys($modifiedModelData),
                        [
                            
                                'progress',
                            
                                'continueOnError',
                            
                                'step',
                            
                                'command',
                            
                        ]
                    )
                ) {
                    
                        $compositionErrorCollection[] = $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex];
                    

                    if (
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex]->getErrors()
                        
                    ) {
                        throw new \Exception();
                    }
                } else {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c217($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;

                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c217)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => 'Schema_Itemofarraysteps65d4b00f3c217',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    
                        if (isset($validatorIndex)) {
                            $this->_propertyValidationState[$validatorIndex][$validatorComponentIndex] = $this->_errorRegistry;
                        }
                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_b96fb($originalModelData, $value));
                }
            
                
            }
            
        } catch (\Exception $e) {
            
                
            

            
                $modelData['progress'] = null;
            
                $modelData['continueOnError'] = null;
            
                $modelData['step'] = null;
            
                $modelData['command'] = null;
            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    

    
        $value = $proposedValue;
    

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    $result = !($succeededCompositionElements === 1);

    
        if ($result) {
            $this->_propertyValidationState = $originalPropertyValidationState;
        }
    

    return $result;
})($value)
) {
        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\ComposedValue\OneOfException($value ?? null, ...array (
  0 => 'Schema_Itemofarraysteps65d4b00f3c28b',
  1 => $succeededCompositionElements,
  2 => $compositionErrorCollection,
)));
    }

    foreach (array_keys($modifiedModelData) as $property) {
        $modifiedModelData[$property] = $modelData[$property];
    }
}


private function validateData_ComposedProperty_65d4b00f3c1f8(&$value): void {
                    
            $succeededCompositionElements = 0;
            $compositionErrorCollection = [];
        
                    
                    if (
(function (&$value) use (
    &$modelData,
    &$modifiedModelData,
    &$compositionErrorCollection,
    &$succeededCompositionElements,
    &$validatorIndex
) {
    $succeededCompositionElements = 2;
    $validatorComponentIndex = 0;
    $originalModelData = $value;
    $proposedValue = null;
    $modifiedValues = [];

    

    
        $originalErrorRegistry = $this->_errorRegistry;
    

    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_c6562($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'data',
  1 => 'string',
)));
}

                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_c6562($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    

    
        $value = $proposedValue;
    

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    $result = !($succeededCompositionElements > 0);

    

    return $result;
})($value)
) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\ComposedValue\AnyOfException($value ?? null, ...array (
  0 => 'data',
  1 => $succeededCompositionElements,
  2 => $compositionErrorCollection,
)));
                    }
                }

private function validateCommand_ComposedProperty_65d4b00f3c25c(&$value): void {
                    
            $succeededCompositionElements = 0;
            $compositionErrorCollection = [];
        
                    
                    if (
(function (&$value) use (
    &$modelData,
    &$modifiedModelData,
    &$compositionErrorCollection,
    &$succeededCompositionElements,
    &$validatorIndex
) {
    $succeededCompositionElements = 1;
    $validatorComponentIndex = 0;
    $originalModelData = $value;
    $proposedValue = null;
    $modifiedValues = [];

    

    
        $originalErrorRegistry = $this->_errorRegistry;
    

    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                

                
                    

if (!is_array($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'command',
  1 => 'array',
)));
}

                
                    $this->validateCommand_ArrayItem_65d4b00f3c250($value);
                

                
                    $compositionErrorCollection[] = $this->_errorRegistry;

                    

                    // an error inside the composed validation occurred. Throw an exception to count the validity of the
                    // composition item
                    if ($this->_errorRegistry->getErrors()) {
                        throw new \Exception();
                    }
                

                
                    $proposedValue = $proposedValue ?? $value;
                

                if (is_object($value)) {
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_33543($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    

    
        $value = $proposedValue;
    

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    $result = !($succeededCompositionElements > 0);

    

    return $result;
})($value)
) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\ComposedValue\AnyOfException($value ?? null, ...array (
  0 => 'command',
  1 => $succeededCompositionElements,
  2 => $compositionErrorCollection,
)));
                    }
                }


                        private function _getModifiedValues_b96fb(array $originalModelData, object $nestedCompositionObject): array {
                            $modifiedValues = [];
                            $defaultValueMap = array (
);
    
                            foreach (array (
  'progress' => 'getProgress',
  'continueOnError' => 'getContinueOnError',
  'step' => 'getStep',
  'slug' => 'getSlug',
  'fromPath' => 'getFromPath',
  'toPath' => 'getToPath',
  'consts' => 'getConsts',
  'siteUrl' => 'getSiteUrl',
  'file' => 'getFile',
  'pluginZipFile' => 'getPluginZipFile',
  'options' => 'getOptions',
  'themeZipFile' => 'getThemeZipFile',
  'path' => 'getPath',
  'code' => 'getCode',
  'sql' => 'getSql',
  'zipFile' => 'getZipFile',
  'zipPath' => 'getZipPath',
  'extractToPath' => 'getExtractToPath',
  'data' => 'getData',
  'command' => 'getCommand',
) as $key => $accessor) {
                                if ((isset($originalModelData[$key]) || in_array($key, $defaultValueMap))
                                    && method_exists($nestedCompositionObject, $accessor)
                                    && ($modifiedValue = $nestedCompositionObject->$accessor()) !== ($originalModelData[$key] ?? !$modifiedValue)
                                ) {
                                    $modifiedValues[$key] = $modifiedValue;
                                }
                            }
    
                            return $modifiedValues;
                        }


                        private function _getModifiedValues_c6562(array $originalModelData, object $nestedCompositionObject): array {
                            $modifiedValues = [];
                            $defaultValueMap = array (
);
    
                            foreach (array (
) as $key => $accessor) {
                                if ((isset($originalModelData[$key]) || in_array($key, $defaultValueMap))
                                    && method_exists($nestedCompositionObject, $accessor)
                                    && ($modifiedValue = $nestedCompositionObject->$accessor()) !== ($originalModelData[$key] ?? !$modifiedValue)
                                ) {
                                    $modifiedValues[$key] = $modifiedValue;
                                }
                            }
    
                            return $modifiedValues;
                        }


                        private function _getModifiedValues_33543(array $originalModelData, object $nestedCompositionObject): array {
                            $modifiedValues = [];
                            $defaultValueMap = array (
);
    
                            foreach (array (
) as $key => $accessor) {
                                if ((isset($originalModelData[$key]) || in_array($key, $defaultValueMap))
                                    && method_exists($nestedCompositionObject, $accessor)
                                    && ($modifiedValue = $nestedCompositionObject->$accessor()) !== ($originalModelData[$key] ?? !$modifiedValue)
                                ) {
                                    $modifiedValues[$key] = $modifiedValue;
                                }
                            }
    
                            return $modifiedValues;
                        }

private function validateCommand_ArrayItem_65d4b00f3c250(&$value): void {
                    $invalidItems_b5d3f8742cf5838dfe73097d04a820bc = [];
                    
                    if (is_array($value) && (function (&$items) use (&$invalidItems_b5d3f8742cf5838dfe73097d04a820bc) {
    
        $originalErrorRegistry = $this->_errorRegistry;
    

    foreach ($items as $index => &$value) {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        try {
            

            
                

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'item of array command',
  1 => 'string',
)));
}

            

            
                if ($this->_errorRegistry->getErrors()) {
                    $invalidItems_b5d3f8742cf5838dfe73097d04a820bc[$index] = $this->_errorRegistry->getErrors();
                }
            
        } catch (\Exception $e) {
            // collect all errors concerning invalid items
            isset($invalidItems_b5d3f8742cf5838dfe73097d04a820bc[$index])
                ? $invalidItems_b5d3f8742cf5838dfe73097d04a820bc[$index][] = $e
                : $invalidItems_b5d3f8742cf5838dfe73097d04a820bc[$index] = [$e];
        }
    }

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    return !empty($invalidItems_b5d3f8742cf5838dfe73097d04a820bc);
})($value)) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Arrays\InvalidItemException($value ?? null, ...array (
  0 => 'command',
  1 => $invalidItems_b5d3f8742cf5838dfe73097d04a820bc,
)));
                    }
                }


}

// @codeCoverageIgnoreEnd
