<?php

namespace Mbedzinski\LaravelModuleBuilder\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as BaseCommand;

class ModelBuildCommand extends BaseCommand
{
    public $signature = 'module:model {name}';
    
    public $description = 'Create module model';
    
    protected function getDefaultNamespace($rootNamespace)
    {
        $moduleName = $this->getNameInput();
        $config     = config("laravel_module_builder.structures.default");
        $baseDir    = $config[ 'baseDir' ].'\\'.$moduleName;
        
        $path      = $config[ 'paths' ][ 'models' ];
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
