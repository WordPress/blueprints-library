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
	public function before() {
		$this->document_root = Path::makeAbsolute( 'test', sys_get_temp_dir() );

		$this->subscriber = new class() implements EventSubscriberInterface {
			public static function getSubscribedEvents() {
				return array(
					ProgressEvent::class => 'onProgress',
					DoneEvent::class     => 'onDone',
				);
			}

			protected $progress_bar;

			public function __construct() {
				ProgressBar::setFormatDefinition( 'custom', ' [%bar%] %current%/%max% -- %message%' );

				$this->progress_bar = ( new SymfonyStyle(
					new StringInput( '' ),
					new ConsoleOutput()
				) )->createProgressBar( 100 );
				$this->progress_bar->setFormat( 'custom' );
				$this->progress_bar->setMessage( 'Start' );
				$this->progress_bar->start();
			}

			public function onProgress( ProgressEvent $event ) {
				$this->progress_bar->setMessage( $event->caption );
				$this->progress_bar->setProgress( (int) $event->progress );
			}

			public function onDone( DoneEvent $event ) {
				$this->progress_bar->finish();
			}
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