<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands\SubCommands;

use Illuminate\Support\Str;
use Mbedzinski\LaravelModuleBuilder\Commands\LaravelModuleBaseCommand;

class MakeControllerCommand extends LaravelModuleBaseCommand
{
    public $signature = 'make:module-controller
    {module}
    {name}
    
    {--structure=default}
    {--r}
    {--m=}
    {--i}
    ';
    
    public $description = 'Create module controller';
    
    public function handle(): void
    {
        try {
            $this->prepareHandle($this);
            
            $controllerName = Str::ucfirst($this->argument('name'));
            $controllerName = str_replace('Controller', '', $controllerName);
            
            $path = $this->getPartNamespace('controllers', $controllerName.'Controller');
            
            $this->call('make:controller', [
                'name' => $path,
                '-r'   => $this->option('r'),
                '-m'   => $this->option('m'),
                '-i'   => $this->option('i'),
            ]);
        } catch (\Exception $error) {
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
}
