<?php

namespace E2E;

use PHPUnit\Framework\TestCase;
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

class JsonBlueprintTest extends TestCase {
	/**
	 * @var string
	 */
	private string $document_root;

	/**
	 * @before
	 */
	public function before() {
		$this->document_root = Path::makeAbsolute( 'test', sys_get_temp_dir() );
	}

	/**
	 * @after
	 */
	public function after() {
		( new Filesystem() )->remove( $this->document_root );
	}
	public function testUntilRunner() {
		$blueprint = '{"steps":[{"step":"mkdir","path":"dir"},{"step": "rm","path": "dir"}]}';

		$subscriber = new class() implements EventSubscriberInterface {
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

		$results = run_blueprint(
			$blueprint,
			array(
				'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
				'documentRoot'       => $this->document_root . '/new-wp',
				'progressSubscriber' => $subscriber,
			)
		);

		$this->assertEquals( array(), $results );
	}
}
