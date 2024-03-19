<?php

namespace WordPress\Blueprints\Model;

use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep;
use WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\DataClass\DownloadWordPressStep;
use WordPress\Blueprints\Model\DataClass\ImportFileStep;
use WordPress\Blueprints\Model\DataClass\InstallPluginStep;
use WordPress\Blueprints\Model\DataClass\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\DataClass\InstallThemeStep;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Model\DataClass\RunSQLStep;
use WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep;
use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\Blueprints\Model\DataClass\WordPressInstallationOptions;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;


class BlueprintBuilder {

	/**
	 * @var Blueprint
	 */
	private $blueprint;

	public function __construct() {
		$this->blueprint = new Blueprint();
		$this->blueprint->setConstants( new \ArrayObject() );
		$this->blueprint->setSiteOptions( new \ArrayObject() );
	}

	public static function create() {
		return new static();
	}

	public function toBlueprint() {
		return $this->blueprint;
	}

	public function withWordPressVersion( $v ) {
		$this->blueprint->setWordPressVersion( $v );

		return $this;
	}

	/**
	 * @param mixed[] $options
	 */
	public function withSiteOptions( $options ) {
		$this->blueprint->setSiteOptions( $options );

		return $this;
	}

	/**
	 * @param mixed[] $constants
	 */
	public function withWpConfigConstants( $constants ) {
		$this->blueprint->setConstants( $constants );

		return $this;
	}

	public function withPlugins( $pluginZips ) {
		$this->blueprint->setPlugins(
			array_merge( $this->blueprint->plugins, $pluginZips )
		);

		return $this;
	}

	public function withPlugin( $pluginZip ) {
		$this->withPlugins( array( $pluginZip ) );

		return $this;
	}

	public function withThemes( $themeZips ) {
		foreach ( $themeZips as $themeZip ) {
			$this->withTheme( $themeZip );
		}

		return $this;
	}

	public function withTheme( $themeZip ) {
		return $this->addStep(
			( new InstallThemeStep() )
				->setThemeZipFile( $themeZip )
		);
	}

	public function withContent( $wxrs ) {
		if ( ! is_array( $wxrs ) ) {
			$wxrs = array( $wxrs );
		}
		// @TODO: Should this automatically add the importer plugin if it's not already installed?
		foreach ( $wxrs as $wxr ) {
			$this->addStep(
				( new ImportFileStep() )
					->setFile( $wxr )
			);
		}

		return $this;
	}

	public function andRunSQL( $sql ) {
		return $this->addStep(
			( new RunSQLStep() )
				->setSql( $sql )
		);
	}

	public function withSiteUrl( $url ) {
		return $this->addStep(
			( new DefineSiteUrlStep() )
				->setSiteUrl( $url )
		);
	}

	public function withFiles( $files ) {
		foreach ( $files as $path => $data ) {
			$this->withFile( $path, $data );
		}

		return $this;
	}

	public function withFile( $path, $data ) {
		return $this->addStep(
			( new WriteFileStep() )
				->setPath( $path )
				->setData( $data )
		);
	}

	public function downloadWordPress( $wpZip = null ) {
		$this->prependStep(
			( new DownloadWordPressStep() )
				->setWordPressZip(
					$wpZip ?? 'https://wordpress.org/latest.zip'
				)
		);

		return $this;
	}

	public function runInstallationWizard() {
		return $this->addStep(
			( new RunWordPressInstallerStep() )->setOptions( new WordPressInstallationOptions() )
		);
	}

	public function useSqlite(
		$sqlitePluginSource = 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip'
	) {
		$this->addStep(
			( new InstallSqliteIntegrationStep() )
				->setSqlitePluginZip(
					$sqlitePluginSource
				)
		);

		return $this;
	}

	public function downloadWpCli() {
		return $this->addStep(
			( new WriteFileStep() )
				->setPath( 'wp-cli.phar' )
				->setData( ( new UrlResource() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) )
		);
	}

	private function prependStep( StepDefinitionInterface $builder ) {
		array_unshift( $this->blueprint->steps, $builder );

		return $this;
	}

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\StepDefinitionInterface $builder
	 */
	public function addStep( $builder ) {
		array_push( $this->blueprint->steps, $builder );

		return $this;
	}
}
