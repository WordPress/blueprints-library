<?php

namespace e2e;

use e2e\resources\TestResourceClassSimpleSubscriber;
use E2ETestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Compile\StepSuccess;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WordPressInstallationOptions;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
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

		$this->subscriber = new TestResourceClassSimpleSubscriber();
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
				'documentRoot'       => $this->document_root,
				'progressSubscriber' => $this->subscriber,
			)
		);

		$word_press_zip = ( new UrlResource() )
			->setResource( 'url' )
			->setUrl('https://wordpress.org/latest.zip');
		$download_word_press_step = ( new DownloadWordPressStep() )
			->setWordPressZip( $word_press_zip );

		$sqlite_plugin_zip = ( new UrlResource() )
			->setResource('url' )
			->setUrl( 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' );
		$install_sqlite_integration_step = ( new InstallSqliteIntegrationStep() )
			->setSqlitePluginZip( $sqlite_plugin_zip );

		$wp_cli = ( new UrlResource() )
			->setResource('url' )
			->setUrl('https://playground.wordpress.net/wp-cli.phar' );
		$write_file_step = ( new WriteFileStep() )
			->setPath( 'wp-cli.phar' )
			->setData( $wp_cli );

		$run_word_press_installer_step = ( new RunWordPressInstallerStep() )
			->setOptions( new WordPressInstallationOptions() );

		$expected = array(
			0 => new StepSuccess( $download_word_press_step, null ),
			1 => new StepSuccess( $install_sqlite_integration_step, null ),
			2 => new StepSuccess( $write_file_step, null ),
			3 => new StepSuccess( $run_word_press_installer_step, 'Success: WordPress installed successfully.' )
		);

//	 -        'result' => 'Success: WordPress installed successfully.'
//   +        'result' => '#!/usr/bin/env php\n
//   +        Success: WordPress installed successfully.'

		//@TODO Assert WP files exist

		self::assertEquals( $expected, $results );
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
				'documentRoot'       => $this->document_root,
				'progressSubscriber' => $this->subscriber,
			)
		);

		$expected = array(
			0 => new StepSuccess( ( new MkdirStep() )->setPath( 'dir1' ), null ),
			1 => new StepSuccess( ( new RmStep() )->setPath( 'dir1' ), null ),
			2 => new StepSuccess( ( new MkdirStep() )->setPath( 'dir2' ), null ),
		);

		self::assertDirectoryDoesNotExist( Path::makeAbsolute( 'dir1', $this->document_root ) );
		self::assertDirectoryExists( Path::makeAbsolute( 'dir2', $this->document_root ) );
		self::assertEquals( $expected, $results );
	}
}
