<?php

namespace WordPress\Blueprints\Model\DataClass;

class ModelInfo
{
	public static function getResourceDefinitionInterfaceImplementations(): array
	{
		return [
			FilesystemResource::class,
			InlineResource::class,
			CoreThemeResource::class,
			CorePluginResource::class,
			UrlResource::class
		];
	}


	public static function getStepDefinitionInterfaceImplementations(): array
	{
		return [
			ActivatePluginStep::class,
			ActivateThemeStep::class,
			CpStep::class,
			DefineWpConfigConstsStep::class,
			DefineSiteUrlStep::class,
			EnableMultisiteStep::class,
			ImportFileStep::class,
			InstallPluginStep::class,
			InstallThemeStep::class,
			MkdirStep::class,
			MvStep::class,
			RmStep::class,
			RmDirStep::class,
			RunPHPStep::class,
			RunWordPressInstallerStep::class,
			RunSQLStep::class,
			SetSiteOptionsStep::class,
			DownloadWordPressStep::class,
			InstallSqliteIntegrationStep::class,
			UnzipStep::class,
			WriteFileStep::class,
			WPCLIStep::class
		];
	}
}
