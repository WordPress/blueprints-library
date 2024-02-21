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
    class FileInfoNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\FileInfo';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\FileInfo;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\FileInfo();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('key', $data)) {
                $object->setKey($data['key']);
            }
            if (\array_key_exists('name', $data)) {
                $object->setName($data['name']);
            }
            if (\array_key_exists('type', $data)) {
                $object->setType($data['type']);
            }
            if (\array_key_exists('data', $data)) {
                $object->setData($this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData', 'json', $context));
            }
            return $object;
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $data = [];
            $data['key'] = $object->getKey();
            $data['name'] = $object->getName();
            $data['type'] = $object->getType();
            $data['data'] = $this->normalizer->normalize($object->getData(), 'json', $context);
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\FileInfo' => false];
        }
    }
} else {
    class FileInfoNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\FileInfo';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\FileInfo;
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
            $object = new \WordPress\Blueprints\Generated\Model\FileInfo();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('key', $data)) {
                $object->setKey($data['key']);
            }
            if (\array_key_exists('name', $data)) {
                $object->setName($data['name']);
            }
            if (\array_key_exists('type', $data)) {
                $object->setType($data['type']);
            }
            if (\array_key_exists('data', $data)) {
                $object->setData($this->denormalizer->denormalize($data['data'], 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData', 'json', $context));
            }
            return $object;
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $data = [];
            $data['key'] = $object->getKey();
            $data['name'] = $object->getName();
            $data['type'] = $object->getType();
            $data['data'] = $this->normalizer->normalize($object->getData(), 'json', $context);
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\FileInfo' => false];
        }
    }
}