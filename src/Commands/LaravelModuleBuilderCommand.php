<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;

class LaravelModuleBuilderCommand extends Command
{
    private array $structure;
    private string $module;
    
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
    
    public $description = 'My command';
    
    private function preparePath(string $destination = null): string
    {
        $path = base_path();
        $path .= $this->structure[ 'baseDir' ];
        $path .= '/'.$this->module;
        
        File::deleteDirectory($path);
        File::deleteDirectory($path.'/Models');
        
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
            
            $baseNamespace = config("laravel_module_builder.structures.default.baseNamespace").'\\'.$this->module;
            $modelsPath    = config("laravel_module_builder.structures.default.paths.models");
            $modelsPath    = str_replace('{base_dir}', $baseNamespace, $modelsPath).'\\'.$this->module;
            
            $this->call('make:model', [
                'name' => $modelsPath,
            ]);
            
            if (! $this->option('no-service-provider')) {
                $this->call('module:service-provide', [
                    'name'  => $this->module.'ServiceProvider',
                    'model' => $this->module,
                ]);
            }
            
            $this->comment('All done');
        } catch (\Exception $error){
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
}
