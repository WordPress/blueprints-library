<?php

namespace WordPress\Blueprints\Generated\Normalizer;

use WordPress\Blueprints\Generated\Runtime\Normalizer\CheckArray;
use WordPress\Blueprints\Generated\Runtime\Normalizer\ValidatorTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Kernel;
if (!class_exists(Kernel::class) or (Kernel::MAJOR_VERSION >= 7 or Kernel::MAJOR_VERSION === 6 and Kernel::MINOR_VERSION === 4)) {
    class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        protected $normalizers = array('WordPress\\Blueprints\\Generated\\Model\\Blueprint' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintPreferredVersionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintFeaturesNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintSiteOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FilesystemResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InlineResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InlineResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CoreThemeResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CorePluginResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\UrlResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\UrlResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\Progress' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ProgressNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ActivatePluginStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ActivateThemeStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CpStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CpStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\DefineWpConfigConstsStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\DefineSiteUrlStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\EnableMultisiteStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ImportFileStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallPluginStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallThemeStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStepOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallThemeStepOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\MkdirStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\MvStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\MvStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RmStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RmStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RmDirStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunPHPStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunWordPressInstallerStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunSQLStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\SetSiteOptionsStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\UnzipStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WriteFileStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WPCLIStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallPluginOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfo' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoDataNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoDataBufferNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WordPressInstallationOptionsNormalizer', '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => '\\WordPress\\Blueprints\\Generated\\Runtime\\Normalizer\\ReferenceNormalizer'), $normalizersCache = [];
        public function supportsDenormalization($data, $type, $format = null, array $context = []) : bool
        {
            return array_key_exists($type, $this->normalizers);
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return is_object($data) && array_key_exists(get_class($data), $this->normalizers);
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $normalizerClass = $this->normalizers[get_class($object)];
            $normalizer = $this->getNormalizer($normalizerClass);
            return $normalizer->normalize($object, $format, $context);
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            $denormalizerClass = $this->normalizers[$type];
            $denormalizer = $this->getNormalizer($denormalizerClass);
            return $denormalizer->denormalize($data, $type, $format, $context);
        }
        private function getNormalizer(string $normalizerClass)
        {
            return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
        }
        private function initNormalizer(string $normalizerClass)
        {
            $normalizer = new $normalizerClass();
            $normalizer->setNormalizer($this->normalizer);
            $normalizer->setDenormalizer($this->denormalizer);
            $this->normalizersCache[$normalizerClass] = $normalizer;
            return $normalizer;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\Blueprint' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\InlineResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\UrlResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\Progress' => false, 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\CpStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStepOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\MvStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RmStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfo' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer' => false, 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => false, '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => false];
        }
    }
} else {
    class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        protected $normalizers = array('WordPress\\Blueprints\\Generated\\Model\\Blueprint' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintPreferredVersionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintFeaturesNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\BlueprintSiteOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FilesystemResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InlineResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InlineResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CoreThemeResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CorePluginResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\UrlResource' => 'WordPress\\Blueprints\\Generated\\Normalizer\\UrlResourceNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\Progress' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ProgressNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ActivatePluginStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ActivateThemeStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\CpStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\CpStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\DefineWpConfigConstsStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\DefineSiteUrlStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\EnableMultisiteStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\ImportFileStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallPluginStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallThemeStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStepOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallThemeStepOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\MkdirStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\MvStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\MvStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RmStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RmStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RmDirStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunPHPStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunWordPressInstallerStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\RunSQLStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\SetSiteOptionsStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\UnzipStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WriteFileStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WPCLIStepNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\InstallPluginOptionsNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfo' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoDataNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer' => 'WordPress\\Blueprints\\Generated\\Normalizer\\FileInfoDataBufferNormalizer', 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => 'WordPress\\Blueprints\\Generated\\Normalizer\\WordPressInstallationOptionsNormalizer', '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => '\\WordPress\\Blueprints\\Generated\\Runtime\\Normalizer\\ReferenceNormalizer'), $normalizersCache = [];
        public function supportsDenormalization($data, $type, $format = null, array $context = []) : bool
        {
            return array_key_exists($type, $this->normalizers);
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return is_object($data) && array_key_exists(get_class($data), $this->normalizers);
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $normalizerClass = $this->normalizers[get_class($object)];
            $normalizer = $this->getNormalizer($normalizerClass);
            return $normalizer->normalize($object, $format, $context);
        }
        /**
         * @return mixed
         */
        public function denormalize($data, $type, $format = null, array $context = [])
        {
            $denormalizerClass = $this->normalizers[$type];
            $denormalizer = $this->getNormalizer($denormalizerClass);
            return $denormalizer->denormalize($data, $type, $format, $context);
        }
        private function getNormalizer(string $normalizerClass)
        {
            return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
        }
        private function initNormalizer(string $normalizerClass)
        {
            $normalizer = new $normalizerClass();
            $normalizer->setNormalizer($this->normalizer);
            $normalizer->setDenormalizer($this->denormalizer);
            $this->normalizersCache[$normalizerClass] = $normalizer;
            return $normalizer;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\Blueprint' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintPreferredVersions' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintFeatures' => false, 'WordPress\\Blueprints\\Generated\\Model\\BlueprintSiteOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\InlineResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\UrlResource' => false, 'WordPress\\Blueprints\\Generated\\Model\\Progress' => false, 'WordPress\\Blueprints\\Generated\\Model\\ActivatePluginStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\ActivateThemeStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\CpStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\DefineWpConfigConstsStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\DefineSiteUrlStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\EnableMultisiteStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\ImportFileStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallThemeStepOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\MkdirStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\MvStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RmStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RmDirStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunPHPStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunWordPressInstallerStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\SetSiteOptionsStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\WPCLIStep' => false, 'WordPress\\Blueprints\\Generated\\Model\\InstallPluginOptions' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfo' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => false, 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer' => false, 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => false, '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => false];
        }
    }
}