<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LaravelModuleBuilderCommand extends Command
{
    private array $structure;
    private string $module;
    
    private $model;
    private $controller;
    private $requestCreate;
    private $requestUpdate;
    private $migrations;
    
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
    
    private function getModulePath(): string
    {
        $path = base_path();
        $path .= '/'.$this->structure[ 'baseDir' ];
        $path .= '/'.$this->module;

//        File::deleteDirectory($path);
        
        return $path;
    }
    
    private function prepareModuleStructure()
    {
        $path = $this->getModulePath();
        if (File::isDirectory($path)) {
            throw new \Exception('Module already exists');
        }
        
        return File::makeDirectory($path);
    }
    
    public function handle()
    {
        try {
            $activeStructure = $this->option('structure') == 'default' ? config('laravel_module_builder.structure') : $this->options('structure');
            
            $this->structure = config("laravel_module_builder.structures.{$activeStructure}");
            
            $this->module = ucfirst($this->argument('module'));
            
            //TODO DELETE ME!!
            File::deleteDirectory($this->getModulePath());
            
            $this->prepareModuleStructure();
            
            $this->makeModel();
            $this->makeProvider();
            $this->makeController();
            $this->makeViews();
            $this->makeRoutes();
            $this->makeServices();
            $this->makeRequests();
            $this->makeMigration();
            
            $this->comment('All done');
        } catch (\Exception $error) {
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
    
    private function getPartPath($part)
    {
        $path = config("laravel_module_builder.structures.default.paths.{$part}");
        
        return str_replace('{base_dir}', $this->getModulePath(), $path);
    }
    
    private function getPartNamespace($part, $fileName = null)
    {
        $baseNamespace = config('laravel_module_builder.structures.default.baseNamespace').'\\'.$this->module;
        $path          = config("laravel_module_builder.structures.default.paths.{$part}");
        
        return str_replace('{base_dir}', $baseNamespace, $path).($fileName ? ('\\'.$fileName) : '');
    }
    
    //test here
    
    private function getStub($stub): string
    {
        return File::get(__DIR__."/../Stubs/{$stub}.stub");
    }
    
    protected function makeModel(): self
    {
        $path        = $this->getPartNamespace('models', $this->module);
        $this->model = $path;
        
        $this->call('make:model', [
            'name' => $path,
        ]);
        
        return $this;
    }
    
    protected function makeController(): self
    {
        $path = $this->getPartNamespace('controllers', $this->module.'Controller');
        
        $this->call('make:controller', [
            'name'       => $path,
            '--model'    => $this->model,
            '--resource' => true,
        ]);
        
        $this->controller = $path;
        
        return $this;
    }
    
    protected function makeRequests(): self
    {
        $this->requestCreate = $this->getPartNamespace('requests', $this->module.'CreateRequest');
        $this->requestUpdate = $this->getPartNamespace('requests', $this->module.'UpdateRequest');
        
        $this->call('make:request', [
            'name' => $this->requestCreate,
        ]);
        
        $this->call('make:request', [
            'name' => $this->requestUpdate,
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
        $path = $this->getPartPath('views');
        
        if (File::isDirectory($path)) {
            throw new \Exception('Module views directory already exists');
        }
        
        File::makeDirectory($path, 0755, true);
        
        return $this;
    }
    
    protected function makeRoutes(): self
    {
        $path = $this->getPartPath('routes');
        
        if (File::isDirectory($path)) {
            throw new \Exception('Module routes directory already exists');
        }
        
        File::makeDirectory($path, 0755, true);
        
        $stub = $this->getStub('routes');
        
        $stub = str_replace('{controller_namespace}', $this->controller, $stub);
        $stub = str_replace('{model_name}', strtolower($this->module), $stub);
        $stub = str_replace('{controller_name}', Str::afterLast($this->controller, '\\'), $stub);
        
        File::put($path.'/Routes.php', $stub);
        
        return $this;
    }
    
    protected function makeServices(): self
    {
        $path      = $this->getPartPath('services');
        $namespace = $this->getPartNamespace('services');
        
        if (File::isDirectory($path)) {
            throw new \Exception('Module services directory already exists');
        }
        
        File::makeDirectory($path, 0755, true);
        
        $stub = $this->getStub('services');
        
        $className = $this->module.'Services';
        
        $stub = str_replace('{namespace}', $namespace, $stub);
        $stub = str_replace('{model_namespace}', $this->model, $stub);
        $stub = str_replace('{class_name}', $className, $stub);
        
        File::put("{$path}/{$className}.php", $stub);
        
        return $this;
    }
    
    protected function makeMigration(): self
    {
        $path = $this->getPartPath('migrations');
        
        if (File::isDirectory($path)) {
            throw new \Exception('Module migrations directory already exists');
        }
        
        File::makeDirectory($path, 0755, true);
        
        $this->migrations = $path;
        
        return $this;
    }
}
