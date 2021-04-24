<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaravelModuleBuilderCommand extends Command
{
    private array $structure;
    private string $module;
    
    private $model;
    private $controller;
    
    public $signature = 'make:module
                            {module}
                            {--structure=default}
                            {--no-service-provider}
                            {--no-model}
                            {--no-views}
                            {--no-migrations}
                            {--no-requests}
                            {--no-config}
                            {--no-controller}
                            {--no-helpers}
                            {--no-services}';
    
    public $description = 'Create module';
    
    private function preparePath(): string
    {
        $path = base_path();
        $path .= $this->structure[ 'baseDir' ];
        $path .= '/'.$this->module;
        
        File::deleteDirectory($path);
        
        return $path;
    }
    
    private function prepareModuleStructure()
    {
        $path = $this->preparePath();
        if (File::isDirectory($path)) {
            throw new \Exception('Module already exists');
        }
        
        return File::makeDirectory($path);
    }
    
    public function handle()
    {
        try{
            $activeStructure = $this->option('structure') == 'default' ? config('laravel_module_builder.structure') : $this->options('structure');
            
            $this->structure = config("laravel_module_builder.structures.{$activeStructure}");
            
            $this->module = ucfirst($this->argument('module'));
            
            $this->prepareModuleStructure();
            
            $this->makeModel();
            $this->makeProvider();
            $this->makeController();
            $this->makeViews();
            
            $this->comment('All done');
        } catch (\Exception $error){
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
    
    protected function makeModel(): self
    {
        $baseNamespace = config("laravel_module_builder.structures.default.baseNamespace").'\\'.$this->module;
        $modelsPath    = config("laravel_module_builder.structures.default.paths.models");
        $modelsPath    = str_replace('{base_dir}', $baseNamespace, $modelsPath).'\\'.$this->module;
        
        $this->model = $modelsPath;
        
        $this->call('make:model', [
            'name' => $modelsPath,
        ]);
        
        return $this;
    }
    
    protected function makeController(): self
    {
        $baseNamespace = config("laravel_module_builder.structures.default.baseNamespace").'\\'.$this->module;
        $path          = config("laravel_module_builder.structures.default.paths.controllers");
        $path          = str_replace('{base_dir}', $baseNamespace, $path).'\\'.$this->module;
        
        $this->controller = $path;
        
        $this->call('make:controller', [
            'name'       => $path.'Controller',
            '--model'    => $this->model,
            '--resource' => true,
        ]);
        
        return $this;
    }
    
    protected function makeProvider(): self
    {
        $this->call('module:service-provide', [
            'name'  => $this->module.'ServiceProvider',
            'model' => $this->module,
        ]);
        
        return $this;
    }
    
    protected function makeViews(): self
    {
        $baseDir = base_path().'/'.config("laravel_module_builder.structures.default.baseDir").'/'.$this->module;
        $path    = config("laravel_module_builder.structures.default.paths.views");
        $path    = str_replace('{base_dir}', $baseDir, $path);
        
        if (File::isDirectory($path)) {
            throw new \Exception('Module views directory already exists');
        }
        
        File::makeDirectory($path);
        
        return $this;
    }
    
}
