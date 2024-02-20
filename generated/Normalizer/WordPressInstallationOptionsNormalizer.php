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
    class WordPressInstallationOptionsNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions';
        }
        public function supportsNormalization(mixed $data, string $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\WordPressInstallationOptions;
        }
        public function denormalize(mixed $data, string $type, string $format = null, array $context = []) : mixed
        {
            if (isset($data['$ref'])) {
                return new Reference($data['$ref'], $context['document-origin']);
            }
            if (isset($data['$recursiveRef'])) {
                return new Reference($data['$recursiveRef'], $context['document-origin']);
            }
            $object = new \WordPress\Blueprints\Generated\Model\WordPressInstallationOptions();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WordPressInstallationOptionsConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('adminUsername', $data)) {
                $object->setAdminUsername($data['adminUsername']);
            }
            if (\array_key_exists('adminPassword', $data)) {
                $object->setAdminPassword($data['adminPassword']);
            }
            return $object;
        }
        public function normalize(mixed $object, string $format = null, array $context = []) : array|string|int|float|bool|\ArrayObject|null
        {
            $data = [];
            if ($object->isInitialized('adminUsername') && null !== $object->getAdminUsername()) {
                $data['adminUsername'] = $object->getAdminUsername();
            }
            if ($object->isInitialized('adminPassword') && null !== $object->getAdminPassword()) {
                $data['adminPassword'] = $object->getAdminPassword();
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WordPressInstallationOptionsConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => false];
        }
    }
} else {
    class WordPressInstallationOptionsNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
    {
        use DenormalizerAwareTrait;
        use NormalizerAwareTrait;
        use CheckArray;
        use ValidatorTrait;
        public function supportsDenormalization($data, $type, string $format = null, array $context = []) : bool
        {
            return $type === 'WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions';
        }
        public function supportsNormalization($data, $format = null, array $context = []) : bool
        {
            return $data instanceof \WordPress\Blueprints\Generated\Model\WordPressInstallationOptions;
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
            $object = new \WordPress\Blueprints\Generated\Model\WordPressInstallationOptions();
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WordPressInstallationOptionsConstraint());
            }
            if (null === $data || false === \is_array($data)) {
                return $object;
            }
            if (\array_key_exists('adminUsername', $data)) {
                $object->setAdminUsername($data['adminUsername']);
            }
            if (\array_key_exists('adminPassword', $data)) {
                $object->setAdminPassword($data['adminPassword']);
            }
            return $object;
        }
        /**
         * @return array|string|int|float|bool|\ArrayObject|null
         */
        public function normalize($object, $format = null, array $context = [])
        {
            $data = [];
            if ($object->isInitialized('adminUsername') && null !== $object->getAdminUsername()) {
                $data['adminUsername'] = $object->getAdminUsername();
            }
            if ($object->isInitialized('adminPassword') && null !== $object->getAdminPassword()) {
                $data['adminPassword'] = $object->getAdminPassword();
            }
            if (!($context['skip_validation'] ?? false)) {
                $this->validate($data, new \WordPress\Blueprints\Generated\Validator\WordPressInstallationOptionsConstraint());
            }
            return $data;
        }
        public function getSupportedTypes(?string $format = null) : array
        {
            return ['WordPress\\Blueprints\\Generated\\Model\\WordPressInstallationOptions' => false];
        }
    }
}