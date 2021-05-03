<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

abstract class LaravelModuleBaseCommand extends Command
{
    protected array $structure;
    protected string $module;
    
    protected $model;
    protected $controller;
    protected $requestCreate;
    protected $requestUpdate;
    protected $migrations;
    
    protected function getModulePath(): string
    {
        $path = base_path();
        $path .= '/'.$this->structure[ 'baseDir' ];
        $path .= '/'.$this->module;

//        File::deleteDirectory($path);
        
        return $path;
    }
    
    protected function prepareModuleStructure()
    {
        $path = $this->getModulePath();
        if (File::isDirectory($path)) {
            return;
//            throw new \Exception('Module already exists');
        }
        
        return File::makeDirectory($path, 0755, true);
    }
    
    protected function getPartPath($part)
    {
        $path = config("laravel_module_builder.structures.default.paths.{$part}");
        
        return str_replace('{base_dir}', $this->getModulePath(), $path);
    }
    
    protected function getPartNamespace($part, $fileName = null): string
    {
        $baseNamespace = config('laravel_module_builder.structures.default.baseNamespace').'\\'.$this->module;
        $path          = config("laravel_module_builder.structures.default.paths.{$part}");
        
        return str_replace('{base_dir}', $baseNamespace, $path).($fileName ? ('\\'.$fileName) : '');
    }
    
    protected function getStub($stub): string
    {
        return File::get(__DIR__."/../Stubs/{$stub}.stub");
    }
    
    protected function prepareHandle($command): void
    {
        $activeStructure = $command->option('structure') === 'default'
            ? config('laravel_module_builder.structure')
            : $command->options('structure');
        
        $this->structure = config("laravel_module_builder.structures.{$activeStructure}");
        
        $this->module = ucfirst($command->argument('module'));
        
        $this->prepareModuleStructure();
    }
}
