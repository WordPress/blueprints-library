<?php

namespace WordPress\Blueprints\Generated\Normalizer;

use Jane\Component\JsonSchemaRuntime\Reference;
use WordPress\Blueprints\Generated\Runtime\Normalizer\CheckArray;
use WordPress\Blueprints\Generated\Runtime\Normalizer\ValidatorTrait;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Kernel;
if (!class_exists(Kernel::class) or (Kernel::MAJOR_VERSION >= 7 or Kernel::MAJOR_VERSION === 6 and Kernel::MINOR_VERSION === 4)) {
    class BlueprintNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\Blueprint';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\Blueprint;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\Blueprint();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\BlueprintConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('landingPage', $data)) {
                $object->setLandingPage($data['landingPage']);
            }
            if (\array_key_exists('description', $data)) {
                $object->setDescription($data['description']);
            }
            if (\array_key_exists('preferredVersions', $data)) {
                $object->setPreferredVersions($this->denormalizer->denormalize($data['preferredVersions'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions', 'json', $context));
            }
            if (\array_key_exists('features', $data)) {
                $object->setFeatures($this->denormalizer->denormalize($data['features'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures', 'json', $context));
            }
            if (\array_key_exists('constants', $data)) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data['constants'] as $key => $value) {
                    $values[$key] = $value;
                }
                $object->setConstants($values);
            }
            if (\array_key_exists('plugins', $data)) {
                $values_1 = [];
                foreach ($data['plugins'] as $value_1) {
                    $value_2 = $value_1;
                    if (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['path'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['contents'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['slug'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['slug'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['url'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                    }
                    $values_1[] = $value_2;
                }
                $object->setPlugins($values_1);
            }
            if (\array_key_exists('siteOptions', $data)) {
                $object->setSiteOptions($this->denormalizer->denormalize($data['siteOptions'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions', 'json', $context));
            }
            if (\array_key_exists('phpExtensionBundles', $data)) {
                $values_2 = [];
                foreach ($data['phpExtensionBundles'] as $value_3) {
                    $values_2[] = $value_3;
                }
                $object->setPhpExtensionBundles($values_2);
            }
            if (\array_key_exists('steps', $data)) {
                $values_3 = [];
                foreach ($data['steps'] as $value_4) {
                    $value_5 = $value_4;
                    if (is_array($value_4) and isset($value_4['step']) and isset($value_4['slug'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['slug'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['fromPath']) and isset($value_4['toPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\CpStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['consts'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['siteUrl'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['file'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['pluginZipFile'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['themeZipFile'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['fromPath']) and isset($value_4['toPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\MvStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RmStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['code'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['options'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['sql'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['options'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['extractToPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path']) and isset($value_4['data'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['command'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep', 'json', $context);
                    } elseif (is_string($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_bool($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_null($value_4)) {
                        $value_5 = $value_4;
                    } elseif (isset($value_4)) {
                        $value_5 = $value_4;
                    }
                    $values_3[] = $value_5;
                }
                $object->setSteps($values_3);
            }
            if (\array_key_exists('$schema', $data)) {
                $object->setDollarSchema($data['$schema']);
            }
            return $object;
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $data = [];
            if ($object->isInitialized('landingPage') && null !== $object->getLandingPage()) {
                $data['landingPage'] = $object->getLandingPage();
            }
            if ($object->isInitialized('description') && null !== $object->getDescription()) {
                $data['description'] = $object->getDescription();
            }
            if ($object->isInitialized('preferredVersions') && null !== $object->getPreferredVersions()) {
                $data['preferredVersions'] = $this->normalizer->normalize($object->getPreferredVersions(), 'json', $context);
            }
            if ($object->isInitialized('features') && null !== $object->getFeatures()) {
                $data['features'] = $this->normalizer->normalize($object->getFeatures(), 'json', $context);
            }
            if ($object->isInitialized('constants') && null !== $object->getConstants()) {
                $values = [];
                foreach ($object->getConstants() as $key => $value) {
                    $values[$key] = $value;
                }
                $data['constants'] = $values;
            }
            if ($object->isInitialized('plugins') && null !== $object->getPlugins()) {
                $values_1 = [];
                foreach ($object->getPlugins() as $value_1) {
                    $value_2 = $value_1;
                    if (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    }
                    $values_1[] = $value_2;
                }
                $data['plugins'] = $values_1;
            }
            if ($object->isInitialized('siteOptions') && null !== $object->getSiteOptions()) {
                $data['siteOptions'] = $this->normalizer->normalize($object->getSiteOptions(), 'json', $context);
            }
            if ($object->isInitialized('phpExtensionBundles') && null !== $object->getPhpExtensionBundles()) {
                $values_2 = [];
                foreach ($object->getPhpExtensionBundles() as $value_3) {
                    $values_2[] = $value_3;
                }
                $data['phpExtensionBundles'] = $values_2;
            }
            if ($object->isInitialized('steps') && null !== $object->getSteps()) {
                $values_3 = [];
                foreach ($object->getSteps() as $value_4) {
                    $value_5 = $value_4;
                    if (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_string($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_bool($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_null($value_4)) {
                        $value_5 = $value_4;
                    } elseif (!is_null($value_4)) {
                        $value_5 = $value_4;
                    }
                    $values_3[] = $value_5;
                }
                $data['steps'] = $values_3;
            }
            if ($object->isInitialized('dollarSchema') && null !== $object->getDollarSchema()) {
                $data['$schema'] = $object->getDollarSchema();
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\BlueprintConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\Blueprint' => false];
        }
    }
} else {
    class BlueprintNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\Blueprint';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\Blueprint;
        }
        /**
         * @return mixed
         */
        public function denormalize($data, $type, $format = null, array $context = [])
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\Blueprint();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\BlueprintConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('landingPage', $data)) {
                $object->setLandingPage($data['landingPage']);
            }
            if (\array_key_exists('description', $data)) {
                $object->setDescription($data['description']);
            }
            if (\array_key_exists('preferredVersions', $data)) {
                $object->setPreferredVersions($this->denormalizer->denormalize($data['preferredVersions'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions', 'json', $context));
            }
            if (\array_key_exists('features', $data)) {
                $object->setFeatures($this->denormalizer->denormalize($data['features'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures', 'json', $context));
            }
            if (\array_key_exists('constants', $data)) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data['constants'] as $key => $value) {
                    $values[$key] = $value;
                }
                $object->setConstants($values);
            }
            if (\array_key_exists('plugins', $data)) {
                $values_1 = [];
                foreach ($data['plugins'] as $value_1) {
                    $value_2 = $value_1;
                    if (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['path'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['contents'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['slug'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['slug'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                    } elseif (is_array($value_1) and isset($value_1['resource']) and isset($value_1['url'])) {
                        $value_2 = $this->denormalizer->denormalize($value_1, 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                    }
                    $values_1[] = $value_2;
                }
                $object->setPlugins($values_1);
            }
            if (\array_key_exists('siteOptions', $data)) {
                $object->setSiteOptions($this->denormalizer->denormalize($data['siteOptions'], 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions', 'json', $context));
            }
            if (\array_key_exists('phpExtensionBundles', $data)) {
                $values_2 = [];
                foreach ($data['phpExtensionBundles'] as $value_3) {
                    $values_2[] = $value_3;
                }
                $object->setPhpExtensionBundles($values_2);
            }
            if (\array_key_exists('steps', $data)) {
                $values_3 = [];
                foreach ($data['steps'] as $value_4) {
                    $value_5 = $value_4;
                    if (is_array($value_4) and isset($value_4['step']) and isset($value_4['slug'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['slug'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['fromPath']) and isset($value_4['toPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\CpStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['consts'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['siteUrl'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['file'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['pluginZipFile'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['themeZipFile'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['fromPath']) and isset($value_4['toPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\MvStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RmStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['code'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['options'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['sql'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['options'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['extractToPath'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['path']) and isset($value_4['data'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep', 'json', $context);
                    } elseif (is_array($value_4) and isset($value_4['step']) and isset($value_4['command'])) {
                        $value_5 = $this->denormalizer->denormalize($value_4, 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep', 'json', $context);
                    } elseif (is_string($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_bool($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_null($value_4)) {
                        $value_5 = $value_4;
                    } elseif (isset($value_4)) {
                        $value_5 = $value_4;
                    }
                    $values_3[] = $value_5;
                }
                $object->setSteps($values_3);
            }
            if (\array_key_exists('$schema', $data)) {
                $object->setDollarSchema($data['$schema']);
            }
            return $object;
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $data = [];
            if ($object->isInitialized('landingPage') && null !== $object->getLandingPage()) {
                $data['landingPage'] = $object->getLandingPage();
            }
            if ($object->isInitialized('description') && null !== $object->getDescription()) {
                $data['description'] = $object->getDescription();
            }
            if ($object->isInitialized('preferredVersions') && null !== $object->getPreferredVersions()) {
                $data['preferredVersions'] = $this->normalizer->normalize($object->getPreferredVersions(), 'json', $context);
            }
            if ($object->isInitialized('features') && null !== $object->getFeatures()) {
                $data['features'] = $this->normalizer->normalize($object->getFeatures(), 'json', $context);
            }
            if ($object->isInitialized('constants') && null !== $object->getConstants()) {
                $values = [];
                foreach ($object->getConstants() as $key => $value) {
                    $values[$key] = $value;
                }
                $data['constants'] = $values;
            }
            if ($object->isInitialized('plugins') && null !== $object->getPlugins()) {
                $values_1 = [];
                foreach ($object->getPlugins() as $value_1) {
                    $value_2 = $value_1;
                    if (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_string($value_1)) {
                        $value_2 = $value_1;
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    } elseif (is_object($value_1)) {
                        $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                    }
                    $values_1[] = $value_2;
                }
                $data['plugins'] = $values_1;
            }
            if ($object->isInitialized('siteOptions') && null !== $object->getSiteOptions()) {
                $data['siteOptions'] = $this->normalizer->normalize($object->getSiteOptions(), 'json', $context);
            }
            if ($object->isInitialized('phpExtensionBundles') && null !== $object->getPhpExtensionBundles()) {
                $values_2 = [];
                foreach ($object->getPhpExtensionBundles() as $value_3) {
                    $values_2[] = $value_3;
                }
                $data['phpExtensionBundles'] = $values_2;
            }
            if ($object->isInitialized('steps') && null !== $object->getSteps()) {
                $values_3 = [];
                foreach ($object->getSteps() as $value_4) {
                    $value_5 = $value_4;
                    if (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_object($value_4)) {
                        $value_5 = $this->normalizer->normalize($value_4, 'json', $context);
                    } elseif (is_string($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_bool($value_4)) {
                        $value_5 = $value_4;
                    } elseif (is_null($value_4)) {
                        $value_5 = $value_4;
                    } elseif (!is_null($value_4)) {
                        $value_5 = $value_4;
                    }
                    $values_3[] = $value_5;
                }
                $data['steps'] = $values_3;
            }
            if ($object->isInitialized('dollarSchema') && null !== $object->getDollarSchema()) {
                $data['$schema'] = $object->getDollarSchema();
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\BlueprintConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\Blueprint' => false];
        }
    }
}