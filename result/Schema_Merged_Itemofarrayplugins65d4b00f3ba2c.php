<?php

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

declare(strict_types = 1);


    namespace MyApp\Model;



    use PHPModelGenerator\Interfaces\JSONModelInterface;

    use PHPModelGenerator\Exception\ErrorRegistryException;


/**
 * Class Schema_Merged_Itemofarrayplugins65d4b00f3ba2c
 * @package MyApp\Model 
 *

 * This is an auto-implemented class implemented by the php-json-schema-model-generator.
 * If you need to implement something in this class use inheritance. Else you will lose your changes if the classes
 * are re-generated.
 */
class Schema_Merged_Itemofarrayplugins65d4b00f3ba2c implements JSONModelInterface
{
    

    
        /** @var string Identifies the file resource as Virtual File System (VFS) */
        protected $resource;
    
        /** @var string The path to the file in the VFS */
        protected $path;
    
        /** @var string The contents of the file */
        protected $contents;
    
        /** @var string The slug of the WordPress Core theme */
        protected $slug;
    
        /** @var string The URL of the file */
        protected $url;
    
        /** @var string|null Optional caption for displaying a progress message */
        protected $caption;
    
    /** @var array */
    protected $_rawModelDataInput = [];

    
        /** @var ErrorRegistryException Collect all validation errors */
        protected $_errorRegistry;
    

    /**
     * Schema_Merged_Itemofarrayplugins65d4b00f3ba2c constructor.
     *
     * @param array $rawModelDataInput
     *
     * @throws ErrorRegistryException
     */
    public function __construct(array $rawModelDataInput = [])
    {
        
            $this->_errorRegistry = new ErrorRegistryException();
        

        

        

        
            
                $this->processResource($rawModelDataInput);
            
        
            
                $this->processPath($rawModelDataInput);
            
        
            
                $this->processContents($rawModelDataInput);
            
        
            
                $this->processSlug($rawModelDataInput);
            
        
            
                $this->processUrl($rawModelDataInput);
            
        
            
                $this->processCaption($rawModelDataInput);
            
        

        
            if (count($this->_errorRegistry->getErrors())) {
                throw $this->_errorRegistry;
            }
        

        $this->_rawModelDataInput = $rawModelDataInput;

        
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
             * Get the value of resource.
             *
             * Identifies the file resource as Virtual File System (VFS)
             *
             * @return string
             */
            public function getResource()
                : string
            {
                

                return $this->resource;
            }

            
                /**
                 * Set the value of resource.
                 *
                 * @param string $resource Identifies the file resource as Virtual File System (VFS)
                 *
                 * 
                 *
                 * @return self
                 */
                public function setResource(
                    string $resource
                ): self {
                    if ($this->resource === $resource) {
                        return $this;
                    }

                    $value = $modelData['resource'] = $resource;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateResource($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->resource = $value;
                    $this->_rawModelDataInput['resource'] = $resource;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property resource
             *
             * @param array $modelData
             *
             * 
             */
            protected function processResource(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('resource', $modelData) ? $modelData['resource'] : $this->resource;

                

                $this->resource = $this->validateResource($value, $modelData);
            }

            /**
             * Execute all validators for the property resource
             */
            protected function validateResource($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of path.
             *
             * The path to the file in the VFS
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
                 * @param string $path The path to the file in the VFS
                 *
                 * 
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
             * 
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
                

                return $value;
            }
        
    
        
            /**
             * Get the value of contents.
             *
             * The contents of the file
             *
             * @return string
             */
            public function getContents()
                : string
            {
                

                return $this->contents;
            }

            
                /**
                 * Set the value of contents.
                 *
                 * @param string $contents The contents of the file
                 *
                 * 
                 *
                 * @return self
                 */
                public function setContents(
                    string $contents
                ): self {
                    if ($this->contents === $contents) {
                        return $this;
                    }

                    $value = $modelData['contents'] = $contents;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateContents($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->contents = $value;
                    $this->_rawModelDataInput['contents'] = $contents;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property contents
             *
             * @param array $modelData
             *
             * 
             */
            protected function processContents(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('contents', $modelData) ? $modelData['contents'] : $this->contents;

                

                $this->contents = $this->validateContents($value, $modelData);
            }

            /**
             * Execute all validators for the property contents
             */
            protected function validateContents($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of slug.
             *
             * The slug of the WordPress Core theme
             *
             * @return string
             */
            public function getSlug()
                : string
            {
                

                return $this->slug;
            }

            
                /**
                 * Set the value of slug.
                 *
                 * @param string $slug The slug of the WordPress Core theme
                 *
                 * 
                 *
                 * @return self
                 */
                public function setSlug(
                    string $slug
                ): self {
                    if ($this->slug === $slug) {
                        return $this;
                    }

                    $value = $modelData['slug'] = $slug;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

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
             * Get the value of url.
             *
             * The URL of the file
             *
             * @return string
             */
            public function getUrl()
                : string
            {
                

                return $this->url;
            }

            
                /**
                 * Set the value of url.
                 *
                 * @param string $url The URL of the file
                 *
                 * 
                 *
                 * @return self
                 */
                public function setUrl(
                    string $url
                ): self {
                    if ($this->url === $url) {
                        return $this;
                    }

                    $value = $modelData['url'] = $url;

                    
                        $this->_errorRegistry = new ErrorRegistryException();
                    

                    

                    $value = $this->validateUrl($value, $modelData);

                    
                        if ($this->_errorRegistry->getErrors()) {
                            throw $this->_errorRegistry;
                        }
                    

                    $this->url = $value;
                    $this->_rawModelDataInput['url'] = $url;

                    

                    return $this;
                }
            

            /**
             * Extract the value, perform validations and set the property url
             *
             * @param array $modelData
             *
             * 
             */
            protected function processUrl(array $modelData): void
            {
                
                    
                

                $value = array_key_exists('url', $modelData) ? $modelData['url'] : $this->url;

                

                $this->url = $this->validateUrl($value, $modelData);
            }

            /**
             * Execute all validators for the property url
             */
            protected function validateUrl($value, array $modelData)
            {
                

                return $value;
            }
        
    
        
            /**
             * Get the value of caption.
             *
             * Optional caption for displaying a progress message
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
                 * @param string $caption Optional caption for displaying a progress message
                 *
                 * 
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
             * 
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
                

                return $value;
            }
        
    

    
}

// @codeCoverageIgnoreEnd
