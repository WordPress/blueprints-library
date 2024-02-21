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
    class RunSQLStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\RunSQLStep;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\RunSQLStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\RunSQLStepConstraint());
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
            if (\array_key_exists('sql', $data)) {
                $value = $data['sql'];
                if (is_string($data['sql'])) {
                    $value = $data['sql'];
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['path'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['url'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                }
                $object->setSql($value);
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
            $value = $object->getSql();
            if (is_string($object->getSql())) {
                $value = $object->getSql();
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            }
            $data['sql'] = $value;
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\RunSQLStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => false];
        }
    }
} else {
    class RunSQLStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\RunSQLStep';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\RunSQLStep;
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
            $object = new \WordPress\Blueprints\Generated\Model\RunSQLStep();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\RunSQLStepConstraint());
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
            if (\array_key_exists('sql', $data)) {
                $value = $data['sql'];
                if (is_string($data['sql'])) {
                    $value = $data['sql'];
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['path'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\FilesystemResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['contents'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\InlineResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\CoreThemeResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['slug'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\CorePluginResource', 'json', $context);
                } elseif (is_array($data['sql']) and isset($data['sql']['resource']) and isset($data['sql']['url'])) {
                    $value = $this->denormalizer->denormalize($data['sql'], 'WordPress\\Blueprints\\Generated\\Model\\UrlResource', 'json', $context);
                }
                $object->setSql($value);
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
            $value = $object->getSql();
            if (is_string($object->getSql())) {
                $value = $object->getSql();
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            } elseif (is_object($object->getSql())) {
                $value = $this->normalizer->normalize($object->getSql(), 'json', $context);
            }
            $data['sql'] = $value;
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\RunSQLStepConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\RunSQLStep' => false];
        }
    }
}