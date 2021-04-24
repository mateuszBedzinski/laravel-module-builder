<?php

namespace Mbedzinski\LaravelModuleBuilder;

use Mbedzinski\LaravelModuleBuilder\Commands\ServiceProviderBuildCommand;
use Mbedzinski\LaravelModuleBuilder\Commands\ModelBuildCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Mbedzinski\LaravelModuleBuilder\Commands\LaravelModuleBuilderCommand;

class LaravelModuleBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel_module_builder')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_module_builder_table')
            ->hasCommand(LaravelModuleBuilderCommand::class)
            ->hasCommand(ServiceProviderBuildCommand::class);
    }
}