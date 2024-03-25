<?php

namespace e2e;

use e2e\resources\TestResourceClassSimpleSubscriber;
use E2ETestCase;
use Opis\JsonSchema\Filters\DataExistsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Compile\StepSuccess;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
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

		$this->subscriber = new TestResourceClassSimpleSubscriber();
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
			0 => new StepSuccess( new DownloadWordPressStep(), null ),
			1 => new StepSuccess( new InstallSqliteIntegrationStep(), null ),
			2 => new StepSuccess( new WriteFileStep(), null ),
			3 => new StepSuccess( new RunWordPressInstallerStep(), null )
		);

		// @TODO fix expected
		$this->assertEquals( $expected, $results );
	}

	public function testRunningJsonBlueprintWithSteps() {
		$blueprint = '{"steps":[{"step":"mkdir","path":"dir1"},{"step": "rm","path": "dir1"},{"step":"mkdir","path":"dir2"}]}';

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
			0 => new StepSuccess( new DefineWpConfigConstsStep(), null ),
			1 => new StepSuccess( new SetSiteOptionsStep(), null ),
			2 => new StepSuccess( new MkdirStep(), null ),
			3 => new StepSuccess( new RmStep(), null ),
			4 => new StepSuccess( new MkdirStep(), null ),
		);

		// @TODO fix expected
		$this->assertEquals( $expected, $results );
	}
}