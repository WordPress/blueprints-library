<?php

namespace e2e;

use E2ETestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;
use function WordPress\Blueprints\run_blueprint;

class JsonBlueprintTest extends E2ETestCase {
	/**
	 * @var string
	 */
	private $document_root;

	/**
	 * @var EventSubscriberInterface
	 */
	private $subscriber;

	/**
	 * @before
	 */
	public function before()
	{
		$this->document_root = Path::makeAbsolute('test', sys_get_temp_dir());

		$this->subscriber = new class implements EventSubscriberInterface {
			public static function getSubscribedEvents() {
				return [
					ProgressEvent::class => 'onProgress',
					DoneEvent::class => 'onDone',
				];
			}

			protected $progress_bar;

			public function __construct() {}

			public function onProgress( ProgressEvent $event ) {
				echo $event->caption . " – " . $event->progress . "%\n";
			}

			public function onDone( DoneEvent $event ) {}
		};
	}

	/**
	 * @after
	 */
	public function after() {
		( new Filesystem() )->remove( $this->document_root );
	}
	public function testRunningJsonBlueprintWithWordPressVersion() {
		$blueprint = '{"WordPressVersion":"https://wordpress.org/latest.zip"}';

		$results = run_blueprint(
			$blueprint,
			array(
				'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
				'documentRoot'       => $this->document_root . '/new-wp',
				'progressSubscriber' => $this->subscriber,
			)
		);

		// fails at downloadWordPress

		$expected = array(
//			0 => new StepSuccess(),
//			1 => new StepSuccess(),
//			2 => new StepSuccess(),
//			3 => new StepSuccess(),
		);

		// @TODO fix expected
		$this->assertEquals( $expected, $results );
	}

	public function testRunningJsonBlueprintWithSteps() {
		$blueprint = '{"steps":[{"step":"mkdir","path":"dir"},{"step": "rm","path": "dir"}]}';

		$results = run_blueprint(
			$blueprint,
			array(
				'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
				'documentRoot'       => $this->document_root . '/new-wp',
				'progressSubscriber' => $this->subscriber,
			)
		);

		// fails at defineWpConfigConsts

		$expected = array(
//			0 => new StepSuccess(),
//			1 => new StepSuccess(),
//			2 => new StepSuccess(),
//			3 => new StepSuccess(),
		);

		// @TODO fix expected
		$this->assertEquals( $expected, $results );
	}
}