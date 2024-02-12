<?php

namespace WordPress\Blueprints;
use Symfony\Component\Process\Process;

function temp_path(string $name=null): string {
    return sys_get_temp_dir() . '/' . random_filename() . ($name ? "-$name" : '');
}

function random_filename(int $length=10): string {    
    $characters = array_merge(range('a','z'), range('A','Z'), range('0','9'));
    return array_rand($characters);
}

function run_php_file(string $path, array $env): mixed {
    $process = new Process(['php', $path]);
    $process->setEnv($env);
    $process->mustRun();
    return $process->getOutput();
}