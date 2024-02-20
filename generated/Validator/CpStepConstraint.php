<?php

namespace WordPress\Blueprints\Generated\Validator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class CpStepConstraint extends Compound {
	protected function getConstraints( $options ): array {
		return [
			new NotNull( [ 'message' => 'This value should not be null.' ] ),
			new Collection( [
				'fields'           => [
					'progress'        => new Optional( [
						new NotNull( [ 'message' => 'This value should not be null.' ] ),
						new \WordPress\Blueprints\Generated\Validator\ProgressConstraint( [] ),
					] ),
					'continueOnError' => new Optional( [
						new Type( [ '0' => 'bool' ] ),
						new NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'step'            => new Required( [
						new Type( [ '0' => 'string' ] ),
						new NotNull( [ 'message' => 'This value should not be null.' ] ),
						new EqualTo( [
							'value'   => 'cp',
							'message' => 'This value should be equal to "{{ compared_value }}".',
						] ),
					] ),
					'fromPath'        => new Required( [
						new Type( [ '0' => 'string' ] ),
						new NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'toPath'          => new Required( [
						new Type( [ '0' => 'string' ] ),
						new NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
				],
				'allowExtraFields' => false,
			] ),
		];
	}
}
