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
    class WriteFileStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\WriteFileStep;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\WriteFileStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WriteFileStepConstraint());
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
            if (\array_key_exists('path', $data)) {
                $object->setPath($data['path']);
            }
            if (\array_key_exists('data', $data)) {
                $value = $data['data'];
                if (is_string($data['data'])) {
                    $value = $data['data'];
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['path'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['url'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                } elseif (is_string($data['data'])) {
                    $value = $data['data'];
                }
                $object->setData($value);
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
            $data['path'] = $object->getPath();
            $value = $object->getData();
            if (is_string($object->getData())) {
                $value = $object->getData();
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_string($object->getData())) {
                $value = $object->getData();
            }
            $data['data'] = $value;
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WriteFileStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => false];
        }
    }
} else {
    class WriteFileStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\WriteFileStep';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\WriteFileStep;
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
            $object = new \WordPress\Blueprints\Generated\Model\WriteFileStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WriteFileStepConstraint());
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
            if (\array_key_exists('path', $data)) {
                $object->setPath($data['path']);
            }
            if (\array_key_exists('data', $data)) {
                $value = $data['data'];
                if (is_string($data['data'])) {
                    $value = $data['data'];
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['path'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['data']) and isset($data['data']['resource']) and isset($data['data']['url'])) {
                    $value = $this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                } elseif (is_string($data['data'])) {
                    $value = $data['data'];
                }
                $object->setData($value);
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
            $data['path'] = $object->getPath();
            $value = $object->getData();
            if (is_string($object->getData())) {
                $value = $object->getData();
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_object($object->getData())) {
                $value = $this->normalizer->normalize($object->getData(), 'json', $context);
            } elseif (is_string($object->getData())) {
                $value = $object->getData();
            }
            $data['data'] = $value;
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WriteFileStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\WriteFileStep' => false];
        }
    }
}