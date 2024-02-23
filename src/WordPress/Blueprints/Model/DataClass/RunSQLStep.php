<?php

namespace WordPress\Blueprints\Model\DataClass;

class RunSQLStep implements StepDefinitionInterface
{
	public const DISCRIMINATOR = 'runSql';

	/** @var Progress */
	public $progress;

	/** @var bool */
	public $continueOnError;

	/**
	 * The step identifier.
	 * @var string
	 */
	public $step = 'runSql';

	/** @var string|ResourceDefinitionInterface */
	public $sql;


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


	public function setSql($sql)
	{
		$this->sql = $sql;
		return $this;
	}
}
