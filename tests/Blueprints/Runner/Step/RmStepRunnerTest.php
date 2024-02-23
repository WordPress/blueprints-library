<?php

namespace Blueprints\Runner\Step;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\Runner\Step\RmStepRunner;
use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

class RmStepRunnerTest extends TestCase
{
    /**
     * @var string
     */
    private $documentRoot;

    /**
     * @var NativePHPRuntime
     */
    private $runtime;

    /**
     * @var RmStepRunner
     */
    private $step;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @before
     */
    public function before()
    {
        $this->documentRoot = Path::makeAbsolute("test", sys_get_temp_dir());
        $this->runtime = new NativePHPRuntime($this->documentRoot);

        $this->step = new RmStepRunner();
        $this->step->setRuntime($this->runtime);

        $this->fileSystem = new Filesystem();
    }

    /**
     * @after
     */
    public function after()
    {
        $this->fileSystem->remove($this->documentRoot);
    }

    public function shouldRemoveDirectoryWhenUsingAbsolutePath()
    {
        $absolutePath = $this->runtime->resolvePath("dir");
        $this->fileSystem->mkdir($absolutePath);

        $input = new RmStep();
        $input->path = $absolutePath;

        $this->step->run($input);

        $this->assertDirectoryNotExists($absolutePath);
    }
}
