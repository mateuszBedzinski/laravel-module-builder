<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands\SubCommands;

use Illuminate\Support\Str;
use Mbedzinski\LaravelModuleBuilder\Commands\LaravelModuleBaseCommand;

class MakeModelCommand extends LaravelModuleBaseCommand
{
    public $signature = 'make:module-model {module} {model}
    {--structure=default}
    {--m}
    {--c}
    ';
    
    public $description = 'Create module model';
    
    public function handle(): void
    {
        try {
            $this->prepareHandle($this);
            
            $modelName = Str::ucfirst(Str::camel($this->argument('model')));
            
            $path        = $this->getPartNamespace('models', $modelName);
            $this->model = $path;
            
            $this->call('make:model', [
                'name' => $this->model,
            ]);
            
            if ($this->option('c')) {
                $this->call('make:module-controller', [
                    'module' => $this->module,
                    'name'   => $modelName,
                    '--r'    => true,
                    '--m'    => $path,
                ]);
            }
            
            if ($this->option('m')) {
                $this->call('make:module-migration', [
                    'module'    => $this->module,
                    'migration' => 'create_'.(Str::snake(Str::plural($modelName))).'_table',
                ]);
            }
        } catch (\Exception $error) {
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
}
