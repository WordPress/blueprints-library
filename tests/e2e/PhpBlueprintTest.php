<?php

namespace e2e;

use E2ETestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Compile\StepSuccess;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;
use function WordPress\Blueprints\run_blueprint;

class PhpBlueprintTest extends E2ETestCase {
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
	public function before() {
		$this->document_root = Path::makeAbsolute( 'test', sys_get_temp_dir() );

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

	public function testRunningPhpBlueprintWithWordPressVersion() {
		$blueprint = BlueprintBuilder::create()
			->withWordPressVersion( 'https://wordpress.org/latest.zip' )
			->toBlueprint();

		$results = run_blueprint(
			$blueprint,
			array(
				'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
				'documentRoot'       => $this->document_root . '/new-wp',
				'progressSubscriber' => $this->subscriber,
			)
		);

		$expected = array();

		// @TODO fix expected
		$this->assertEquals( $expected, $results );
	}

	public function testRunningPhpBlueprintWithSteps() {
		$blueprint = BlueprintBuilder::create()
			->addStep( ( new MkdirStep() )->setPath( 'dir1' ) )
			->addStep( ( new RmStep() )->setPath( 'dir1' ) )
			->addStep( ( new MkdirStep() )->setPath( 'dir2' ) )
			->toBlueprint();

		$results = run_blueprint(
			$blueprint,
			array(
				'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
				'documentRoot'       => $this->document_root . '/new-wp',
				'progressSubscriber' => $this->subscriber,
			)
		);

		$expected = array();
			array(
				0 => new StepSuccess( new MkdirStep(), true ),
				1 => new StepSuccess( new RmStep(), true ),
				2 => new StepSuccess( new MkdirStep(), true ),
			);

			// @TODO fix expected
			$this->assertEquals( $expected, $results );
	}
}
