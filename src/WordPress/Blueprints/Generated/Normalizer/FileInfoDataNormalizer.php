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
    class FileInfoDataNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\FileInfoData;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\FileInfoData();
            if (\array_key_exists('BYTES_PER_ELEMENT', $data) && \is_int($data['BYTES_PER_ELEMENT'])) {
                $data['BYTES_PER_ELEMENT'] = (double) $data['BYTES_PER_ELEMENT'];
            }
            if (\array_key_exists('byteLength', $data) && \is_int($data['byteLength'])) {
                $data['byteLength'] = (double) $data['byteLength'];
            }
            if (\array_key_exists('byteOffset', $data) && \is_int($data['byteOffset'])) {
                $data['byteOffset'] = (double) $data['byteOffset'];
            }
            if (\array_key_exists('length', $data) && \is_int($data['length'])) {
                $data['length'] = (double) $data['length'];
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoDataConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('BYTES_PER_ELEMENT', $data)) {
                $object->setBYTESPERELEMENT($data['BYTES_PER_ELEMENT']);
                unset($data['BYTES_PER_ELEMENT']);
            }
            if (\array_key_exists('buffer', $data)) {
                $object->setBuffer($this->denormalizer->denormalize($data['buffer'], 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer', 'json', $context));
                unset($data['buffer']);
            }
            if (\array_key_exists('byteLength', $data)) {
                $object->setByteLength($data['byteLength']);
                unset($data['byteLength']);
            }
            if (\array_key_exists('byteOffset', $data)) {
                $object->setByteOffset($data['byteOffset']);
                unset($data['byteOffset']);
            }
            if (\array_key_exists('length', $data)) {
                $object->setLength($data['length']);
                unset($data['length']);
            }
            foreach ($data as $key => $value) {
                if (preg_match('/.*/', (string) $key)) {
                    $object[$key] = $value;
                }
            }
            return $object;
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $data = [];
            $data['BYTES_PER_ELEMENT'] = $object->getBYTESPERELEMENT();
            $data['buffer'] = $this->normalizer->normalize($object->getBuffer(), 'json', $context);
            $data['byteLength'] = $object->getByteLength();
            $data['byteOffset'] = $object->getByteOffset();
            $data['length'] = $object->getLength();
            foreach ($object as $key => $value) {
                if (preg_match('/.*/', (string) $key)) {
                    $data[$key] = $value;
                }
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoDataConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => false];
        }
    }
} else {
    class FileInfoDataNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\FileInfoData';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\FileInfoData;
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
            $object = new \WordPress\Blueprints\Generated\Model\FileInfoData();
            if (\array_key_exists('BYTES_PER_ELEMENT', $data) && \is_int($data['BYTES_PER_ELEMENT'])) {
                $data['BYTES_PER_ELEMENT'] = (double) $data['BYTES_PER_ELEMENT'];
            }
            if (\array_key_exists('byteLength', $data) && \is_int($data['byteLength'])) {
                $data['byteLength'] = (double) $data['byteLength'];
            }
            if (\array_key_exists('byteOffset', $data) && \is_int($data['byteOffset'])) {
                $data['byteOffset'] = (double) $data['byteOffset'];
            }
            if (\array_key_exists('length', $data) && \is_int($data['length'])) {
                $data['length'] = (double) $data['length'];
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoDataConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('BYTES_PER_ELEMENT', $data)) {
                $object->setBYTESPERELEMENT($data['BYTES_PER_ELEMENT']);
                unset($data['BYTES_PER_ELEMENT']);
            }
            if (\array_key_exists('buffer', $data)) {
                $object->setBuffer($this->denormalizer->denormalize($data['buffer'], 'WordPress\\Blueprints\\Generated\\Model\\FileInfoDataBuffer', 'json', $context));
                unset($data['buffer']);
            }
            if (\array_key_exists('byteLength', $data)) {
                $object->setByteLength($data['byteLength']);
                unset($data['byteLength']);
            }
            if (\array_key_exists('byteOffset', $data)) {
                $object->setByteOffset($data['byteOffset']);
                unset($data['byteOffset']);
            }
            if (\array_key_exists('length', $data)) {
                $object->setLength($data['length']);
                unset($data['length']);
            }
            foreach ($data as $key => $value) {
                if (preg_match('/.*/', (string) $key)) {
                    $object[$key] = $value;
                }
            }
            return $object;
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $data = [];
            $data['BYTES_PER_ELEMENT'] = $object->getBYTESPERELEMENT();
            $data['buffer'] = $this->normalizer->normalize($object->getBuffer(), 'json', $context);
            $data['byteLength'] = $object->getByteLength();
            $data['byteOffset'] = $object->getByteOffset();
            $data['length'] = $object->getLength();
            foreach ($object as $key => $value) {
                if (preg_match('/.*/', (string) $key)) {
                    $data[$key] = $value;
                }
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\FileInfoDataConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\FileInfoData' => false];
        }
    }
}