<?php

namespace WordPress\Blueprints\Model;

use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\Model\Dirty\Blueprint;
use WordPress\Blueprints\Model\Dirty\DefineSiteUrlStep;
use WordPress\Blueprints\Model\Dirty\DefineWpConfigConstsStep;
use WordPress\Blueprints\Model\Dirty\DownloadWordPressStep;
use WordPress\Blueprints\Model\Dirty\ImportFileStep;
use WordPress\Blueprints\Model\Dirty\InstallPluginStep;
use WordPress\Blueprints\Model\Dirty\InstallSqliteIntegrationStep;
use WordPress\Blueprints\Model\Dirty\InstallThemeStep;
use WordPress\Blueprints\Model\Dirty\RunSQLStep;
use WordPress\Blueprints\Model\Dirty\RunWordPressInstallerStep;
use WordPress\Blueprints\Model\Dirty\SetSiteOptionsStep;
use WordPress\Blueprints\Model\Dirty\UrlResource;
use WordPress\Blueprints\Model\Dirty\WordPressInstallationOptions;
use WordPress\Blueprints\Model\Dirty\WriteFileStep;
use WordPress\Blueprints\Model\InternalValidated\FileReferenceInterface;


class BlueprintBuilder {

	private Blueprint $blueprint;

	public function __construct() {
		$this->blueprint = new Blueprint();
	}

	static public function create() {
		return new static();
	}

	public function getBlueprint() {
		return $this->blueprint;
	}

	public function withWordPressVersion( $v ) {
		$this->blueprint->setWpVersion( $v );

		return $this;
	}

	public function withSiteOptions( array $options ) {
		$this->blueprint->setSiteOptions( (object) $options );

		return $this;
	}

	public function withWpConfigConstants( array $constants ) {
		$this->blueprint->setConstants( (object) $constants );

		return $this;
	}

	public function withPlugins( $pluginZips ) {
		$this->blueprint->setPlugins(
			array_merge( $this->blueprint->plugins, $pluginZips )
		);

		return $this;
	}

	public function withPlugin( $pluginZip ) {
		return $this->withPlugins( [ $pluginZip ] );
	}

	public function withThemes( $themeZips ) {
		foreach ( $themeZips as $themeZip ) {
			$this->withTheme( $themeZip );
		}

		return $this;
	}

	public function withTheme( $themeZip ) {
		return $this->addStep(
			InstallThemeStep::create()->setThemeZipFile( $themeZip )
		);
	}

	public function withContent( $wxrs ) {
		if ( ! is_array( $wxrs ) ) {
			$wxrs = [ $wxrs ];
		}
		$this->withPlugin( 'https://downloads.wordpress.org/plugin/wordpress-importer.zip' );
		foreach ( $wxrs as $wxr ) {
			$this->addStep(
				ImportFileStep::create()->setFile( $wxr )
			);
		}

		return $this;
	}

	public function andRunSQL( $sql ) {
		return $this->addStep(
			RunSQLStep::create()
				->setSql( $sql )
		);
	}

	public function withSiteUrl( $url ) {
		return $this->addStep(
			DefineSiteUrlStep::create()
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
			WriteFileStep::create()
				->setPath( 'wordpress.txt' )
				->setData( $data )
		);
	}

	public function downloadWordPress( string|FileReferenceInterface $wpZip = null ) {
		return $this->prependStep( DownloadWordPressStep::create()
			->setWordPressZip(
				$wpZip ?? 'https://wordpress.org/latest.zip'
			) );
	}

	public function runInstallationWizard() {
		return $this->addStep(
			RunWordPressInstallerStep::create()->setOptions( WordPressInstallationOptions::create() )
		);
	}

	public function useSqlite(
		string|FileReferenceInterface $sqlitePluginSource = 'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip'
	) {
		return $this->addStep(
			InstallSqliteIntegrationStep::create()
				->setSqlitePluginZip(
					$sqlitePluginSource
				)
		);

	}

	public function downloadWpCli() {
		return $this->addStep(
			WriteFileStep::create()
				->setPath( 'wp-cli.phar' )
				->setData( UrlResource::create()->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) )
		);
	}

	private function prependStep( ClassStructureContract $builder ) {
		array_unshift( $this->blueprint->steps, $builder );

		return $this;
	}

	public function addStep( ClassStructureContract $builder ) {
		array_push( $this->blueprint->steps, $builder );

		return $this;
	}
}

