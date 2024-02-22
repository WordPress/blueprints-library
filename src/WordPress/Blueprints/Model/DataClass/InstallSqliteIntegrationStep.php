<?php

namespace WordPress\Blueprints\Model\DataClass;

class InstallSqliteIntegrationStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'installSqliteIntegration';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/** @var string */
	public $step = 'installSqliteIntegration';

	/** @var string|ResourceDefinitionInterface */
	public $sqlitePluginZip;


	public function setProgress(Progress $progress)
	{
		$this->progress = $progress;
		return $this;
	}


	public function setContinueOnError(bool $continueOnError)
	{
		$this->continueOnError = $continueOnError;
		return $this;
	}


	public function setStep(string $step)
	{
		$this->step = $step;
		return $this;
	}


	public function setSqlitePluginZip($sqlitePluginZip)
	{
		$this->sqlitePluginZip = $sqlitePluginZip;
		return $this;
	}
}
