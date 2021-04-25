<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;

class ServiceProviderBuildCommand extends ProviderMakeCommand
{
    public $signature = 'module:service-provide {name} {model?}';
    
    public $description = 'Create module service provider';
    
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

//        dd($this->arguments());
        $model = $this->argument('model');

//        $stub = $this->replaceClass($stub, $name);
        $stub = $this->replaceModels($stub, $model);
        
        return $this->replaceNamespace($stub, $name)
                    ->replaceClass($stub, $name);
    }

    protected function replaceModels($stub, $model): string
    {
        if ($model) {
            $baseNamespace = config('laravel_module_builder.structures.default.baseNamespace').'\\'.$model;
            $modelsPath    = config('laravel_module_builder.structures.default.paths.models');
            $modelsPath    = str_replace('{base_dir}', $baseNamespace, $modelsPath).'\\'.$model;
            
            $stub = str_replace('{Model_alias_name}', $model, $stub);
            $stub = str_replace('{Model_alias_class}', $model.'::class', $stub);
            $stub = str_replace('{Model_namespace}', $modelsPath, $stub);
            
            return $stub;
        }
        
        return str_replace('$loader = AliasLoader::getInstance();
        $loader->alias(\'{Model_alias_name}\', {Model_alias_class});', '', $stub);
    }

    protected function getStub()
    {
        return (dirname(__DIR__).'/Stubs/provider.stub');
    }
    
    protected function getDefaultNamespace($rootNamespace)
    {
        $moduleName = str_replace('ServiceProvider', '', $this->getNameInput());
        $config     = config('laravel_module_builder.structures.default');
        $baseDir    = $config[ 'baseDir' ].'\\'.$moduleName;
        
        $path      = $config[ 'paths' ][ 'providers' ];
        $path      = str_replace('{base_dir}', $baseDir, $path);
        $pathParts = explode('\\', $path);
        
        $namespace = '';
        foreach ($pathParts as $pathPart) {
            if ($pathPart == '') {
                continue;
            }
            
            $namespace .= ucfirst($pathPart).'\\';
        }
        
        return rtrim($namespace, '\\');
    }
}
