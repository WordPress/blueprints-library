<?php

namespace WordPress\Blueprints\Generated\Validator;

use WordPress\Blueprints\Generated\Model\UrlResource;

class BlueprintConstraint extends \Symfony\Component\Validator\Constraints\Compound {
	protected function getConstraints( $options ): array {
		return [
			new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
			new \Symfony\Component\Validator\Constraints\Collection( [
				'fields'           => [
					'landingPage'         => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'string' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'description'         => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'string' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'preferredVersions'   => new \Symfony\Component\Validator\Constraints\Optional( [ new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ) ] ),
					'features'            => new \Symfony\Component\Validator\Constraints\Optional( [ new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ) ] ),
					'constants'           => new \Symfony\Component\Validator\Constraints\Optional( [ new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ) ] ),
					'plugins'             => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\All( [
							new \Symfony\Component\Validator\Constraints\AtLeastOneOf( [
								new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'string' ] ),
								new \Symfony\Component\Validator\Constraints\Type( [ '0' => UrlResource::class ] ),
							] ),
						] ),
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'array' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'siteOptions'         => new \Symfony\Component\Validator\Constraints\Optional( [ new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ) ] ),
					'phpExtensionBundles' => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'array' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'steps'               => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'array' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
					'$schema'             => new \Symfony\Component\Validator\Constraints\Optional( [
						new \Symfony\Component\Validator\Constraints\Type( [ '0' => 'string' ] ),
						new \Symfony\Component\Validator\Constraints\NotNull( [ 'message' => 'This value should not be null.' ] ),
					] ),
				],
				'allowExtraFields' => false,
			] ),
		];
	}
}
