<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LaravelModuleBuilderCommand extends LaravelModuleBaseCommand
{
    public $signature = 'make:module {module}
     {--structure=default}
     ';
    
    public $description = 'Create module';
    
    public function handle(): void
    {
        try {
            $this->prepareHandle($this);
            
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
    
    protected function makeModel(): self
    {
        $path        = $this->getPartNamespace('models', $this->module);
        $this->model = $path;
        
        $this->call('make:module-model', [
            'module' => $this->module,
            'model'  => $this->module,
        ]);
        
        return $this;
    }
    
    protected function makeController(): self
    {
        $path = $this->getPartNamespace('controllers', $this->module.'Controller');
        
        $this->call('make:module-controller', [
            'module' => $this->module,
            'name'   => $this->module,
            '--m'    => $this->model,
            '--r'    => true,
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
