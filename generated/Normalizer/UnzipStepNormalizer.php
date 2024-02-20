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
    class UnzipStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\UnzipStep;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\UnzipStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\UnzipStepConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('progress', $data)) {
                $object->setProgress($this->denormalizer->denormalize($data['progress'], 'WordPress\\Blueprints\\Generated\\Model\\Progress', 'json', $context));
            }
            if (\array_key_exists('continueOnError', $data)) {
                $object->setContinueOnError($data['continueOnError']);
            }
            if (\array_key_exists('step', $data)) {
                $object->setStep($data['step']);
            }
            if (\array_key_exists('zipFile', $data)) {
                $value = $data['zipFile'];
                if (is_string($data['zipFile'])) {
                    $value = $data['zipFile'];
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['path'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['url'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                }
                $object->setZipFile($value);
            }
            if (\array_key_exists('zipPath', $data)) {
                $object->setZipPath($data['zipPath']);
            }
            if (\array_key_exists('extractToPath', $data)) {
                $object->setExtractToPath($data['extractToPath']);
            }
            return $object;
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $data = [];
            if ($object->isInitialized('progress') && null !== $object->getProgress()) {
                $data['progress'] = $this->normalizer->normalize($object->getProgress(), 'json', $context);
            }
            if ($object->isInitialized('continueOnError') && null !== $object->getContinueOnError()) {
                $data['continueOnError'] = $object->getContinueOnError();
            }
            $data['step'] = $object->getStep();
            if ($object->isInitialized('zipFile') && null !== $object->getZipFile()) {
                $value = $object->getZipFile();
                if (is_string($object->getZipFile())) {
                    $value = $object->getZipFile();
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                }
                $data['zipFile'] = $value;
            }
            if ($object->isInitialized('zipPath') && null !== $object->getZipPath()) {
                $data['zipPath'] = $object->getZipPath();
            }
            $data['extractToPath'] = $object->getExtractToPath();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\UnzipStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => false];
        }
    }
} else {
    class UnzipStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\UnzipStep';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\UnzipStep;
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
            $object = new \WordPress\Blueprints\Generated\Model\UnzipStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\UnzipStepConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('progress', $data)) {
                $object->setProgress($this->denormalizer->denormalize($data['progress'], 'WordPress\\Blueprints\\Generated\\Model\\Progress', 'json', $context));
            }
            if (\array_key_exists('continueOnError', $data)) {
                $object->setContinueOnError($data['continueOnError']);
            }
            if (\array_key_exists('step', $data)) {
                $object->setStep($data['step']);
            }
            if (\array_key_exists('zipFile', $data)) {
                $value = $data['zipFile'];
                if (is_string($data['zipFile'])) {
                    $value = $data['zipFile'];
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['path'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['zipFile']) and isset($data['zipFile']['resource']) and isset($data['zipFile']['url'])) {
                    $value = $this->denormalizer->denormalize($data['zipFile'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                }
                $object->setZipFile($value);
            }
            if (\array_key_exists('zipPath', $data)) {
                $object->setZipPath($data['zipPath']);
            }
            if (\array_key_exists('extractToPath', $data)) {
                $object->setExtractToPath($data['extractToPath']);
            }
            return $object;
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $data = [];
            if ($object->isInitialized('progress') && null !== $object->getProgress()) {
                $data['progress'] = $this->normalizer->normalize($object->getProgress(), 'json', $context);
            }
            if ($object->isInitialized('continueOnError') && null !== $object->getContinueOnError()) {
                $data['continueOnError'] = $object->getContinueOnError();
            }
            $data['step'] = $object->getStep();
            if ($object->isInitialized('zipFile') && null !== $object->getZipFile()) {
                $value = $object->getZipFile();
                if (is_string($object->getZipFile())) {
                    $value = $object->getZipFile();
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                } elseif (is_object($object->getZipFile())) {
                    $value = $this->normalizer->normalize($object->getZipFile(), 'json', $context);
                }
                $data['zipFile'] = $value;
            }
            if ($object->isInitialized('zipPath') && null !== $object->getZipPath()) {
                $data['zipPath'] = $object->getZipPath();
            }
            $data['extractToPath'] = $object->getExtractToPath();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\UnzipStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\UnzipStep' => false];
        }
    }
}