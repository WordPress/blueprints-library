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

	public function withWordPressVersion( $v ) {
		$this->builder->setWpVersion( $v );

		return $this;
	}

	public function withSiteOptions( array $options ) {
		$this->builder->setSiteOptions( (object) $options );

		return $this;
	}

	public function withWpConfigConstants( array $constants ) {
		$this->builder->setConstants( (object) $constants );

		return $this;
	}

	public function withPlugins( $pluginZips ) {
		$this->builder->setPlugins(
			array_merge( $this->builder->plugins, $pluginZips )
		);

		return $this;
	}

	public function withPlugin( $pluginZip ) {
		$this->withPlugins( [ $pluginZip ] );

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
			( new InstallThemeStepBuilder() )
				->setThemeZipFile( $themeZip )
		);
	}

	public function withContent( $wxrs ) {
		if ( ! is_array( $wxrs ) ) {
			$wxrs = [ $wxrs ];
		}
		$this->withPlugin( 'https://downloads.wordpress.org/plugin/wordpress-importer.zip' );
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

