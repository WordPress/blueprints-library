<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Schema65d4b00f3af34
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Schema65d4b00f3af34 implements JSONModelInterface
{
    

    
        /** @var string|null The URL to navigate to after the blueprint has been run. */
        protected $landingPage;
    
        /** @var string|null Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what. */
        protected $description;
    
        /** @var Schema_PreferredVersions65d4b00f3b0f0|null The preferred PHP and WordPress versions to use. */
        protected $preferredVersions;
    
        /** @var Schema_Features65d4b00f3b44e|null */
        protected $features;
    
        /** @var Schema_Constants65d4b00f3b4a2|null PHP Constants to define on every request */
        protected $constants;
    
        /** @var string[]|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c[]|null WordPress plugins to install and activate */
        protected $plugins;
    
        /** @var Schema_SiteOptions65d4b00f3bb76|null WordPress site options to define */
        protected $siteOptions;
    
        /** @var string[]|null The PHP extensions to use. */
        protected $phpExtensionBundles;
    
        /** @var string[]|mixed[]|bool[]|null[]|Schema_Itemofarraysteps65d4b00f3bc1a[]|Schema_Itemofarraysteps65d4b00f3bd2b[]|Schema_Itemofarraysteps65d4b00f3bd71[]|Schema_Itemofarraysteps65d4b00f3bdb9[]|Schema_Itemofarraysteps65d4b00f3be19[]|Schema_Itemofarraysteps65d4b00f3be55[]|Schema_Itemofarraysteps65d4b00f3be88[]|Schema_Itemofarraysteps65d4b00f3bec0[]|Schema_Itemofarraysteps65d4b00f3bf1f[]|Schema_Itemofarraysteps65d4b00f3bf77[]|Schema_Itemofarraysteps65d4b00f3bfac[]|Schema_Itemofarraysteps65d4b00f3bfed[]|Schema_Itemofarraysteps65d4b00f3c026[]|Schema_Itemofarraysteps65d4b00f3c05a[]|Schema_Itemofarraysteps65d4b00f3c092[]|Schema_Itemofarraysteps65d4b00f3c0e9[]|Schema_Itemofarraysteps65d4b00f3c123[]|Schema_Itemofarraysteps65d4b00f3c166[]|Schema_Itemofarraysteps65d4b00f3c1ae[]|Schema_Itemofarraysteps65d4b00f3c217[] The steps to run after every other operation in this Blueprint was executed. */
        protected $steps;
    
        /** @var string|null */
        protected $schema;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Schema65d4b00f3af34 constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        
            $this->executeBaseValidators($rawModelDataInput);
        

        
            
                $this->processLandingPage($rawModelDataInput);
            
        
            
                $this->processDescription($rawModelDataInput);
            
        
            
                $this->processPreferredVersions($rawModelDataInput);
            
        
            
                $this->processFeatures($rawModelDataInput);
            
        
            
                $this->processConstants($rawModelDataInput);
            
        
            
                $this->processPlugins($rawModelDataInput);
            
        
            
                $this->processSiteOptions($rawModelDataInput);
            
        
            
                $this->processPhpExtensionBundles($rawModelDataInput);
            
        
            
                $this->processSteps($rawModelDataInput);
            
        
            
                $this->processSchema($rawModelDataInput);
            
        

        
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
   'landingPage',
   'description',
   'preferredVersions',
   'features',
   'constants',
   'plugins',
   'siteOptions',
   'phpExtensionBundles',
   'steps',
   '$schema',
));

    

    return $additionalProperties;
})()) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\AdditionalPropertiesException($value ?? null, ...array (
  0 => 'Schema_Schema65d4b00f3af34',
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
             * Get the value of landingPage.
             *
             * The URL to navigate to after the blueprint has been run.
             *
             * @return string|null
             */
            public function getLandingPage()
                : ?string
            {
                

                return $this->landingPage;
            }

            
                /**
                 * Set the value of landingPage.
                 *
                 * @param string $landingPage The URL to navigate to after the blueprint has been run.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setLandingPage(
                    string $landingPage
                ): self {
                    if ($this->landingPage === $landingPage) {
                        return $this;
                    }

                    $value = $modelData['landingPage'] = $landingPage;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateLandingPage($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->landingPage = $value;
                    $this->_rawModelDataInput['landingPage'] = $landingPage;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property landingPage
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processLandingPage(array $modelData): void
            {
                
                    
                        if (!array_key_exists('landingPage', $modelData) && $this->landingPage === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('landingPage', $modelData) ? $modelData['landingPage'] : $this->landingPage;

                

                $this->landingPage = $this->validateLandingPage($value, $modelData);
            }

            /**
             * Execute all validators for the property landingPage
             */
            protected function validateLandingPage($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'landingPage',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of description.
             *
             * Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
             *
             * @return string|null
             */
            public function getDescription()
                : ?string
            {
                

                return $this->description;
            }

            
                /**
                 * Set the value of description.
                 *
                 * @param string $description Optional description. It doesn't do anything but is exposed as a courtesy to developers who may want to document which blueprint file does what.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setDescription(
                    string $description
                ): self {
                    if ($this->description === $description) {
                        return $this;
                    }

                    $value = $modelData['description'] = $description;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateDescription($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->description = $value;
                    $this->_rawModelDataInput['description'] = $description;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property description
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processDescription(array $modelData): void
            {
                
                    
                        if (!array_key_exists('description', $modelData) && $this->description === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('description', $modelData) ? $modelData['description'] : $this->description;

                

                $this->description = $this->validateDescription($value, $modelData);
            }

            /**
             * Execute all validators for the property description
             */
            protected function validateDescription($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'description',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of preferredVersions.
             *
             * The preferred PHP and WordPress versions to use.
             *
             * @return Schema_PreferredVersions65d4b00f3b0f0|null
             */
            public function getPreferredVersions()
                : ?Schema_PreferredVersions65d4b00f3b0f0
            {
                

                return $this->preferredVersions;
            }

            
                /**
                 * Set the value of preferredVersions.
                 *
                 * @param Schema_PreferredVersions65d4b00f3b0f0 $preferredVersions The preferred PHP and WordPress versions to use.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setPreferredVersions(
                    Schema_PreferredVersions65d4b00f3b0f0 $preferredVersions
                ): self {
                    if ($this->preferredVersions === $preferredVersions) {
                        return $this;
                    }

                    $value = $modelData['preferredVersions'] = $preferredVersions;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validatePreferredVersions($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->preferredVersions = $value;
                    $this->_rawModelDataInput['preferredVersions'] = $preferredVersions;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property preferredVersions
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processPreferredVersions(array $modelData): void
            {
                
                    
                        if (!array_key_exists('preferredVersions', $modelData) && $this->preferredVersions === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('preferredVersions', $modelData) ? $modelData['preferredVersions'] : $this->preferredVersions;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_PreferredVersions65d4b00f3b0f0($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'preferredVersions',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->preferredVersions = $this->validatePreferredVersions($value, $modelData);
            }

            /**
             * Execute all validators for the property preferredVersions
             */
            protected function validatePreferredVersions($value, array $modelData)
            {
                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'preferredVersions',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_PreferredVersions65d4b00f3b0f0)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'preferredVersions',
  1 => 'Schema_PreferredVersions65d4b00f3b0f0',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of features.
             *
             * 
             *
             * @return Schema_Features65d4b00f3b44e|null
             */
            public function getFeatures()
                : ?Schema_Features65d4b00f3b44e
            {
                

                return $this->features;
            }

            
                /**
                 * Set the value of features.
                 *
                 * @param Schema_Features65d4b00f3b44e $features
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setFeatures(
                    Schema_Features65d4b00f3b44e $features
                ): self {
                    if ($this->features === $features) {
                        return $this;
                    }

                    $value = $modelData['features'] = $features;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateFeatures($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->features = $value;
                    $this->_rawModelDataInput['features'] = $features;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property features
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processFeatures(array $modelData): void
            {
                
                    
                        if (!array_key_exists('features', $modelData) && $this->features === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('features', $modelData) ? $modelData['features'] : $this->features;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Features65d4b00f3b44e($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'features',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->features = $this->validateFeatures($value, $modelData);
            }

            /**
             * Execute all validators for the property features
             */
            protected function validateFeatures($value, array $modelData)
            {
                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'features',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Features65d4b00f3b44e)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'features',
  1 => 'Schema_Features65d4b00f3b44e',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of constants.
             *
             * PHP Constants to define on every request
             *
             * @return Schema_Constants65d4b00f3b4a2|null
             */
            public function getConstants()
                : ?Schema_Constants65d4b00f3b4a2
            {
                

                return $this->constants;
            }

            
                /**
                 * Set the value of constants.
                 *
                 * @param Schema_Constants65d4b00f3b4a2 $constants PHP Constants to define on every request
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setConstants(
                    Schema_Constants65d4b00f3b4a2 $constants
                ): self {
                    if ($this->constants === $constants) {
                        return $this;
                    }

                    $value = $modelData['constants'] = $constants;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateConstants($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->constants = $value;
                    $this->_rawModelDataInput['constants'] = $constants;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property constants
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processConstants(array $modelData): void
            {
                
                    
                        if (!array_key_exists('constants', $modelData) && $this->constants === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('constants', $modelData) ? $modelData['constants'] : $this->constants;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Constants65d4b00f3b4a2($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'constants',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->constants = $this->validateConstants($value, $modelData);
            }

            /**
             * Execute all validators for the property constants
             */
            protected function validateConstants($value, array $modelData)
            {
                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'constants',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Constants65d4b00f3b4a2)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'constants',
  1 => 'Schema_Constants65d4b00f3b4a2',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of plugins.
             *
             * WordPress plugins to install and activate
             *
             * @return string[]|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c[]|null
             */
            public function getPlugins()
                : ?array
            {
                

                return $this->plugins;
            }

            
                /**
                 * Set the value of plugins.
                 *
                 * @param string[]|Schema_Merged_Itemofarrayplugins65d4b00f3ba2c[] $plugins WordPress plugins to install and activate
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setPlugins(
                    array $plugins
                ): self {
                    if ($this->plugins === $plugins) {
                        return $this;
                    }

                    $value = $modelData['plugins'] = $plugins;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validatePlugins($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->plugins = $value;
                    $this->_rawModelDataInput['plugins'] = $plugins;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property plugins
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processPlugins(array $modelData): void
            {
                
                    
                        if (!array_key_exists('plugins', $modelData) && $this->plugins === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('plugins', $modelData) ? $modelData['plugins'] : $this->plugins;

                

                $this->plugins = $this->validatePlugins($value, $modelData);
            }

            /**
             * Execute all validators for the property plugins
             */
            protected function validatePlugins($value, array $modelData)
            {
                
                    

if (!is_array($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'plugins',
  1 => 'array',
)));
}

                
                    $this->validatePlugins_ArrayItem_65d4b00f3bb68($value);
                

                return $value;
            }
        
    
        
            /**
             * Get the value of siteOptions.
             *
             * WordPress site options to define
             *
             * @return Schema_SiteOptions65d4b00f3bb76|null
             */
            public function getSiteOptions()
                : ?Schema_SiteOptions65d4b00f3bb76
            {
                

                return $this->siteOptions;
            }

            
                /**
                 * Set the value of siteOptions.
                 *
                 * @param Schema_SiteOptions65d4b00f3bb76 $siteOptions WordPress site options to define
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setSiteOptions(
                    Schema_SiteOptions65d4b00f3bb76 $siteOptions
                ): self {
                    if ($this->siteOptions === $siteOptions) {
                        return $this;
                    }

                    $value = $modelData['siteOptions'] = $siteOptions;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateSiteOptions($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->siteOptions = $value;
                    $this->_rawModelDataInput['siteOptions'] = $siteOptions;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property siteOptions
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processSiteOptions(array $modelData): void
            {
                
                    
                        if (!array_key_exists('siteOptions', $modelData) && $this->siteOptions === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('siteOptions', $modelData) ? $modelData['siteOptions'] : $this->siteOptions;

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_SiteOptions65d4b00f3bb76($value) : $value;
    } catch (\Exception $instantiationException) {
        
            $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\NestedObjectException($value ?? null, ...array (
  0 => 'siteOptions',
  1 => $instantiationException,
)));
        

        
            return $instantiationException;
        
    }
})($value)
;

                $this->siteOptions = $this->validateSiteOptions($value, $modelData);
            }

            /**
             * Execute all validators for the property siteOptions
             */
            protected function validateSiteOptions($value, array $modelData)
            {
                
                    

if (!is_object($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'siteOptions',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_SiteOptions65d4b00f3bb76)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'siteOptions',
  1 => 'Schema_SiteOptions65d4b00f3bb76',
)));
}

                

                return $value;
            }
        
    
        
            /**
             * Get the value of phpExtensionBundles.
             *
             * The PHP extensions to use.
             *
             * @return string[]|null
             */
            public function getPhpExtensionBundles()
                : ?array
            {
                

                return $this->phpExtensionBundles;
            }

            
                /**
                 * Set the value of phpExtensionBundles.
                 *
                 * @param string[] $phpExtensionBundles The PHP extensions to use.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setPhpExtensionBundles(
                    array $phpExtensionBundles
                ): self {
                    if ($this->phpExtensionBundles === $phpExtensionBundles) {
                        return $this;
                    }

                    $value = $modelData['phpExtensionBundles'] = $phpExtensionBundles;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validatePhpExtensionBundles($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->phpExtensionBundles = $value;
                    $this->_rawModelDataInput['phpExtensionBundles'] = $phpExtensionBundles;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property phpExtensionBundles
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processPhpExtensionBundles(array $modelData): void
            {
                
                    
                        if (!array_key_exists('phpExtensionBundles', $modelData) && $this->phpExtensionBundles === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('phpExtensionBundles', $modelData) ? $modelData['phpExtensionBundles'] : $this->phpExtensionBundles;

                

                $this->phpExtensionBundles = $this->validatePhpExtensionBundles($value, $modelData);
            }

            /**
             * Execute all validators for the property phpExtensionBundles
             */
            protected function validatePhpExtensionBundles($value, array $modelData)
            {
                
                    

if (!is_array($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'phpExtensionBundles',
  1 => 'array',
)));
}

                
                    $this->validatePhpExtensionBundles_ArrayItem_65d4b00f3bbc1($value);
                

                return $value;
            }
        
    
        
            /**
             * Get the value of steps.
             *
             * The steps to run after every other operation in this Blueprint was executed.
             *
             * @return string[]|mixed[]|bool[]|null[]|Schema_Itemofarraysteps65d4b00f3bc1a[]|Schema_Itemofarraysteps65d4b00f3bd2b[]|Schema_Itemofarraysteps65d4b00f3bd71[]|Schema_Itemofarraysteps65d4b00f3bdb9[]|Schema_Itemofarraysteps65d4b00f3be19[]|Schema_Itemofarraysteps65d4b00f3be55[]|Schema_Itemofarraysteps65d4b00f3be88[]|Schema_Itemofarraysteps65d4b00f3bec0[]|Schema_Itemofarraysteps65d4b00f3bf1f[]|Schema_Itemofarraysteps65d4b00f3bf77[]|Schema_Itemofarraysteps65d4b00f3bfac[]|Schema_Itemofarraysteps65d4b00f3bfed[]|Schema_Itemofarraysteps65d4b00f3c026[]|Schema_Itemofarraysteps65d4b00f3c05a[]|Schema_Itemofarraysteps65d4b00f3c092[]|Schema_Itemofarraysteps65d4b00f3c0e9[]|Schema_Itemofarraysteps65d4b00f3c123[]|Schema_Itemofarraysteps65d4b00f3c166[]|Schema_Itemofarraysteps65d4b00f3c1ae[]|Schema_Itemofarraysteps65d4b00f3c217[]
             */
            public function getSteps()
                : ?array
            {
                

                return $this->steps;
            }

            
                /**
                 * Set the value of steps.
                 *
                 * @param string[]|mixed[]|bool[]|null[]|Schema_Itemofarraysteps65d4b00f3bc1a[]|Schema_Itemofarraysteps65d4b00f3bd2b[]|Schema_Itemofarraysteps65d4b00f3bd71[]|Schema_Itemofarraysteps65d4b00f3bdb9[]|Schema_Itemofarraysteps65d4b00f3be19[]|Schema_Itemofarraysteps65d4b00f3be55[]|Schema_Itemofarraysteps65d4b00f3be88[]|Schema_Itemofarraysteps65d4b00f3bec0[]|Schema_Itemofarraysteps65d4b00f3bf1f[]|Schema_Itemofarraysteps65d4b00f3bf77[]|Schema_Itemofarraysteps65d4b00f3bfac[]|Schema_Itemofarraysteps65d4b00f3bfed[]|Schema_Itemofarraysteps65d4b00f3c026[]|Schema_Itemofarraysteps65d4b00f3c05a[]|Schema_Itemofarraysteps65d4b00f3c092[]|Schema_Itemofarraysteps65d4b00f3c0e9[]|Schema_Itemofarraysteps65d4b00f3c123[]|Schema_Itemofarraysteps65d4b00f3c166[]|Schema_Itemofarraysteps65d4b00f3c1ae[]|Schema_Itemofarraysteps65d4b00f3c217[] $steps The steps to run after every other operation in this Blueprint was executed.
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setSteps(
                    array $steps
                ): self {
                    if ($this->steps === $steps) {
                        return $this;
                    }

                    $value = $modelData['steps'] = $steps;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateSteps($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->steps = $value;
                    $this->_rawModelDataInput['steps'] = $steps;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property steps
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processSteps(array $modelData): void
            {
                
                    
                        if (!array_key_exists('steps', $modelData) && $this->steps === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('steps', $modelData) ? $modelData['steps'] : $this->steps;

                

                $this->steps = $this->validateSteps($value, $modelData);
            }

            /**
             * Execute all validators for the property steps
             */
            protected function validateSteps($value, array $modelData)
            {
                
                    

if (!is_array($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'steps',
  1 => 'array',
)));
}

                
                    $this->validateSteps_ArrayItem_65d4b00f3c505($value);
                

                return $value;
            }
        
    
        
            /**
             * Get the value of $schema.
             *
             * 
             *
             * @return string|null
             */
            public function getSchema()
                : ?string
            {
                

                return $this->schema;
            }

            
                /**
                 * Set the value of $schema.
                 *
                 * @param string $schema
                 *
                 * @throws ErrorRegistryException
                 *
                 * @return self
                 */
                public function setSchema(
                    string $schema
                ): self {
                    if ($this->schema === $schema) {
                        return $this;
                    }

                    $value = $modelData['$schema'] = $schema;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateSchema($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->schema = $value;
                    $this->_rawModelDataInput['$schema'] = $schema;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property schema
             *
             * @param array $modelData
             *
             * @throws ErrorRegistryException
             */
            protected function processSchema(array $modelData): void
            {
                
                    
                        if (!array_key_exists('$schema', $modelData) && $this->schema === null) {
                            return;
                        }
                    
                

                $value = array_key_exists('$schema', $modelData) ? $modelData['$schema'] : $this->schema;

                

                $this->schema = $this->validateSchema($value, $modelData);
            }

            /**
             * Execute all validators for the property schema
             */
            protected function validateSchema($value, array $modelData)
            {
                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => '$schema',
  1 => 'string',
)));
}

                

                return $value;
            }
        
    

    private function validatePlugins_ArrayItem_65d4b00f3bb68(&$value): void {
                    $invalidItems_632057b18481b76cbe71c78b86275801 = [];
                    
                    if (is_array($value) && (function (&$items) use (&$invalidItems_632057b18481b76cbe71c78b86275801) {
    
        $originalErrorRegistry = $this->_errorRegistry;
    

    foreach ($items as $index => &$value) {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        try {
            

            
                $this->validateItemOfArrayPlugins_ComposedProperty_65d4b00f3bb2c($value);
            

            
                if ($this->_errorRegistry->getErrors()) {
                    $invalidItems_632057b18481b76cbe71c78b86275801[$index] = $this->_errorRegistry->getErrors();
                }
            
        } catch (\Exception $e) {
            // collect all errors concerning invalid items
            isset($invalidItems_632057b18481b76cbe71c78b86275801[$index])
                ? $invalidItems_632057b18481b76cbe71c78b86275801[$index][] = $e
                : $invalidItems_632057b18481b76cbe71c78b86275801[$index] = [$e];
        }
    }

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    return !empty($invalidItems_632057b18481b76cbe71c78b86275801);
})($value)) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Arrays\InvalidItemException($value ?? null, ...array (
  0 => 'plugins',
  1 => $invalidItems_632057b18481b76cbe71c78b86275801,
)));
                    }
                }

private function validatePhpExtensionBundles_ArrayItem_65d4b00f3bbc1(&$value): void {
                    $invalidItems_18533bc0ecb84ac2004c095f11545d3d = [];
                    
                    if (is_array($value) && (function (&$items) use (&$invalidItems_18533bc0ecb84ac2004c095f11545d3d) {
    
        $originalErrorRegistry = $this->_errorRegistry;
    

    foreach ($items as $index => &$value) {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        try {
            

            
                

if ($value !== 'kitchen-sink') {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'item of array phpExtensionBundles',
  1 => 'kitchen-sink',
)));
}

            

            
                if ($this->_errorRegistry->getErrors()) {
                    $invalidItems_18533bc0ecb84ac2004c095f11545d3d[$index] = $this->_errorRegistry->getErrors();
                }
            
        } catch (\Exception $e) {
            // collect all errors concerning invalid items
            isset($invalidItems_18533bc0ecb84ac2004c095f11545d3d[$index])
                ? $invalidItems_18533bc0ecb84ac2004c095f11545d3d[$index][] = $e
                : $invalidItems_18533bc0ecb84ac2004c095f11545d3d[$index] = [$e];
        }
    }

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    return !empty($invalidItems_18533bc0ecb84ac2004c095f11545d3d);
})($value)) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Arrays\InvalidItemException($value ?? null, ...array (
  0 => 'phpExtensionBundles',
  1 => $invalidItems_18533bc0ecb84ac2004c095f11545d3d,
)));
                    }
                }

private function validateSteps_ArrayItem_65d4b00f3c505(&$value): void {
                    $invalidItems_26bac3b864e3e68291033f71170da9a3 = [];
                    
                    if (is_array($value) && (function (&$items) use (&$invalidItems_26bac3b864e3e68291033f71170da9a3) {
    
        $originalErrorRegistry = $this->_errorRegistry;
    

    foreach ($items as $index => &$value) {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        try {
            

            
                $this->validateItemOfArraySteps_ComposedProperty_65d4b00f3c500($value);
            

            
                if ($this->_errorRegistry->getErrors()) {
                    $invalidItems_26bac3b864e3e68291033f71170da9a3[$index] = $this->_errorRegistry->getErrors();
                }
            
        } catch (\Exception $e) {
            // collect all errors concerning invalid items
            isset($invalidItems_26bac3b864e3e68291033f71170da9a3[$index])
                ? $invalidItems_26bac3b864e3e68291033f71170da9a3[$index][] = $e
                : $invalidItems_26bac3b864e3e68291033f71170da9a3[$index] = [$e];
        }
    }

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    return !empty($invalidItems_26bac3b864e3e68291033f71170da9a3);
})($value)) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Arrays\InvalidItemException($value ?? null, ...array (
  0 => 'steps',
  1 => $invalidItems_26bac3b864e3e68291033f71170da9a3,
)));
                    }
                }

private function validateItemOfArrayPlugins_ComposedProperty_65d4b00f3bb2c(&$value): void {
                    
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
                

                

                

                
                    

if (!is_string($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'item of array plugins',
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_2c62e($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_2c62e($originalModelData, $value));
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
  0 => 'item of array plugins',
  1 => $succeededCompositionElements,
  2 => $compositionErrorCollection,
)));
                    }
                }

private function validateItemOfArraySteps_ComposedProperty_65d4b00f3c500(&$value): void {
                    
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
    $succeededCompositionElements = 5;
    $validatorComponentIndex = 0;
    $originalModelData = $value;
    $proposedValue = null;
    $modifiedValues = [];

    

    
        $originalErrorRegistry = $this->_errorRegistry;
    

    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c28b($value) : $value;
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
  0 => 'item of array steps',
  1 => 'object',
)));
}

                
                    

if (is_object($value) && !($value instanceof \Exception) && !($value instanceof Schema_Itemofarraysteps65d4b00f3c28b)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Object\InvalidInstanceOfException($value ?? null, ...array (
  0 => 'item of array steps',
  1 => 'Schema_Itemofarraysteps65d4b00f3c28b',
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_a60e8($originalModelData, $value));
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
  0 => 'item of array steps',
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_a60e8($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_a60e8($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                

                
                    

if ($value !== false) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidConstException($value ?? null, ...array (
  0 => 'item of array steps',
  1 => false,
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_a60e8($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    
        try {
            

                
                    // collect errors for each composition element
                    $this->_errorRegistry = new ErrorRegistryException();
                

                

                

                
                    

if (!is_null($value)) {
    $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\Generic\InvalidTypeException($value ?? null, ...array (
  0 => 'item of array steps',
  1 => 'null',
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
                    $modifiedValues = array_merge($modifiedValues, $this->_getModifiedValues_a60e8($originalModelData, $value));
                }
            
        } catch (\Exception $e) {
            

            

            $succeededCompositionElements--;
        }

        $value = $originalModelData;
        $validatorComponentIndex++;
    

    
        if (is_object($proposedValue)) {
            if ($modifiedValues) {
                $value = array_merge($value, $modifiedValues);
            }

            $value = (function ($value) {
    try {
        return is_array($value) ? new Schema_Itemofarraysteps65d4b00f3c28b($value) : $value;
    } catch (\Exception $instantiationException) {
        
            
                foreach($instantiationException->getErrors() as $nestedValidationError) {
                    $this->_errorRegistry->addError($nestedValidationError);
                }
            
        

        
            return $instantiationException;
        
    }
})($value)
;
        } else {
            $value = $proposedValue;
        }
    

    
        $this->_errorRegistry = $originalErrorRegistry;
    

    $result = !($succeededCompositionElements > 0);

    

    return $result;
})($value)
) {
                        $this->_errorRegistry->addError(new \PHPModelGenerator\Exception\ComposedValue\AnyOfException($value ?? null, ...array (
  0 => 'item of array steps',
  1 => $succeededCompositionElements,
  2 => $compositionErrorCollection,
)));
                    }
                }


                        private function _getModifiedValues_2c62e(array $originalModelData, object $nestedCompositionObject): array {
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


                        private function _getModifiedValues_a60e8(array $originalModelData, object $nestedCompositionObject): array {
                            $modifiedValues = [];
                            $defaultValueMap = array (
  0 => 'propertyValidationState',
);
    
                            foreach (array (
  'step' => 'getStep',
  'progress' => 'getProgress',
  'continueOnError' => 'getContinueOnError',
  'slug' => 'getSlug',
  'fromPath' => 'getFromPath',
  'toPath' => 'getToPath',
  'consts' => 'getConsts',
  'siteUrl' => 'getSiteUrl',
  'item of array plugins' => 'getItemOfArrayPlugins',
  'options' => 'getOptions',
  'path' => 'getPath',
  'code' => 'getCode',
  'zipPath' => 'getZipPath',
  'extractToPath' => 'getExtractToPath',
  'data' => 'getData',
  'command' => 'getCommand',
  'propertyValidationState' => 'get_propertyValidationState',
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


}

// @codeCoverageIgnoreEnd
