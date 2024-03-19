<?php

namespace WordPress\Blueprints\Model\DataClass;

class ModelInfo {

	public static function getResourceDefinitionInterfaceImplementations(): array {
		return array(
			FilesystemResource::class,
			InlineResource::class,
			CoreThemeResource::class,
			CorePluginResource::class,
			UrlResource::class,
		);
	}


	public static function getStepDefinitionInterfaceImplementations(): array {
		return array(
			ActivatePluginStep::class,
			ActivateThemeStep::class,
			CpStep::class,
			DefineWpConfigConstsStep::class,
			DefineSiteUrlStep::class,
			EnableMultisiteStep::class,
			EvalPHPCallbackStep::class,
			ImportFileStep::class,
			InstallPluginStep::class,
			InstallThemeStep::class,
			MkdirStep::class,
			MvStep::class,
			RmStep::class,
			RunPHPStep::class,
			RunWordPressInstallerStep::class,
			RunSQLStep::class,
			SetSiteOptionsStep::class,
			UnzipStep::class,
			DownloadWordPressStep::class,
			InstallSqliteIntegrationStep::class,
			WriteFileStep::class,
			WPCLIStep::class,
		);
	}
}
