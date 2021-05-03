<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands\SubCommands;

use Illuminate\Support\Facades\File;
use Mbedzinski\LaravelModuleBuilder\Commands\LaravelModuleBaseCommand;

class MakeMigrationCommand extends LaravelModuleBaseCommand
{
    public $signature = 'make:module-migration
    {module}
    {migration}
    {--structure=default}
    ';
    
    public $description = 'Create module migration';
    
    public function handle(): void
    {
        try{
            $this->prepareHandle($this);
            
            $path = $this->getPartPath('migrations');
            
            $this->call('make:migration', [
                'name'   => $this->argument('migration'),
                '--path' => str_replace(base_path(), '', $path),
            ]);
        } catch (\Exception $error){
            $this->error('An error occurred: '.$error->getMessage());
            
            return;
        }
    }
}
