<?php

namespace WordPress\Blueprints\Generated\Validator;

class InstallPluginStepConstraint extends \Symfony\Component\Validator\Constraints\Compound
{
    protected function getConstraints($options) : array
    {
        return [new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.']), new \Symfony\Component\Validator\Constraints\Collection(['fields' => ['progress' => new \Symfony\Component\Validator\Constraints\Optional([new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.']), new \WordPress\Blueprints\Generated\Validator\ProgressConstraint([])]), 'continueOnError' => new \Symfony\Component\Validator\Constraints\Optional([new \Symfony\Component\Validator\Constraints\Type(['0' => 'bool']), new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.'])]), 'step' => new \Symfony\Component\Validator\Constraints\Required([new \Symfony\Component\Validator\Constraints\Type(['0' => 'string']), new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.']), new \Symfony\Component\Validator\Constraints\EqualTo(['value' => 'installPlugin', 'message' => 'This value should be equal to "{{ compared_value }}".'])]), 'pluginZipFile' => new \Symfony\Component\Validator\Constraints\Required([new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.'])]), 'options' => new \Symfony\Component\Validator\Constraints\Optional([new \Symfony\Component\Validator\Constraints\NotNull(['message' => 'This value should not be null.']), new \WordPress\Blueprints\Generated\Validator\InstallPluginOptionsConstraint([])])], 'allowExtraFields' => false])];
    }
}