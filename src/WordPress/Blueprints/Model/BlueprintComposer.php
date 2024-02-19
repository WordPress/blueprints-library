<?php

namespace WordPress\Blueprints\Model;

use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\DefineSiteUrlStepBuilder;
use WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder;
use WordPress\Blueprints\Model\Builder\DownloadWordPressStepBuilder;
use WordPress\Blueprints\Model\Builder\ImportFileStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallSqliteIntegrationStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallThemeStepBuilder;
use WordPress\Blueprints\Model\Builder\RunSQLStepBuilder;
use WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder;
use WordPress\Blueprints\Model\Builder\SetSiteOptionsStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlResourceBuilder;
use WordPress\Blueprints\Model\Builder\WordPressInstallationOptionsBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Model\DataClass\FileReferenceInterface;


class BlueprintComposer {

	private BlueprintBuilder $builder;

	public function __construct() {
		$this->builder = new BlueprintBuilder();
	}

	static public function create() {
		return new static();
	}

	public function getBuilder() {
		return $this->builder;
	}

	public function withSiteOptions( array $options ) {
		return $this->addStep(
			( new SetSiteOptionsStepBuilder() )
				->setOptions( (object) $options )
		);
	}

	public function withWpConfigConstants( array $constants ) {
		return $this->addStep(
			( new DefineWpConfigConstsStepBuilder() )
				->setConsts( (object) $constants )
		);
	}

	public function withPlugins( $pluginZips ) {
		foreach ( $pluginZips as $pluginZip ) {
			$this->withPlugin( $pluginZip );
		}

		return $this;
	}

	public function withPlugin( $pluginZip ) {
		return $this->addStep(
			( new InstallPluginStepBuilder() )
				->setPluginZipFile( $pluginZip )
		);
	}

	public function withThemes( $themeZips ) {
		foreach ( $themeZips as $themeZip ) {
			$this->withTheme( $themeZip );
		}

		return $this;
	}

	public function withTheme( $themeZip ) {
		return $this->addStep(
			( new InstallThemeStepBuilder() )
				->setThemeZipFile( $themeZip )
		);
	}

	public function withContent( $wxrs ) {
		if ( ! is_array( $wxrs ) ) {
			$wxrs = [ $wxrs ];
		}
		foreach ( $wxrs as $wxr ) {
			$this->addStep(
				( new ImportFileStepBuilder() )
					->setFile( $wxr )
			);
		}

		return $this;
	}

	public function andRunSQL( $sql ) {
		return $this->addStep(
			( new RunSQLStepBuilder() )
				->setSql( $sql )
		);
	}

	public function withSiteUrl( $url ) {
		return $this->addStep(
			( new DefineSiteUrlStepBuilder() )
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
			( new WriteFileStepBuilder() )
				->setPath( 'wordpress.txt' )
				->setData( $data )
		);
	}

	public function downloadWordPress( string|FileReferenceInterface $wpZip = null ) {
		$this->prependStep( ( new DownloadWordPressStepBuilder() )
			->setWordPressZip(
				$wpZip ?? 'https://wordpress.org/latest.zip'
			) );

		return $this;

	}

	public function runInstallationWizard() {
		return $this->addStep(
			( new RunWordPressInstallerStepBuilder() )->setOptions( new WordPressInstallationOptionsBuilder() )
		);
	}

	public function useSqlite(
		string|FileReferenceInterface $sqlitePluginSource = 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip'
	) {
		$this->addStep( ( new InstallSqliteIntegrationStepBuilder() )
			->setSqlitePluginZip(
				$sqlitePluginSource
			) );

		return $this;

	}

	public function downloadWpCli() {
		return $this->addStep( ( new WriteFileStepBuilder() )
			->setPath( 'wp-cli.phar' )
			->setData( ( new UrlResourceBuilder() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) ) );
	}

	private function prependStep( ClassStructureContract $builder ) {
		array_unshift( $this->builder->steps, $builder );

		return $this;
	}

	public function addStep( ClassStructureContract $builder ) {
		array_push( $this->builder->steps, $builder );

		return $this;
	}
}

