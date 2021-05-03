<?php

namespace Mbedzinski\LaravelModuleBuilder;

use Mbedzinski\LaravelModuleBuilder\Commands\LaravelModuleBuilderCommand;
use Mbedzinski\LaravelModuleBuilder\Commands\ServiceProviderBuildCommand;
use Mbedzinski\LaravelModuleBuilder\Commands\SubCommands\MakeControllerCommand;
use Mbedzinski\LaravelModuleBuilder\Commands\SubCommands\MakeMigrationCommand;
use Mbedzinski\LaravelModuleBuilder\Commands\SubCommands\MakeModelCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommand(ServiceProviderBuildCommand::class)
            ->hasCommand(MakeModelCommand::class)
            ->hasCommand(MakeControllerCommand::class)
            ->hasCommand(MakeMigrationCommand::class);
    }
}
