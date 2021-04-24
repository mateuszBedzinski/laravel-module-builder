<?php

namespace Mbedzinski\LaravelModuleBuilder\Tests;

class MakeModuleCommandTest extends TestCase
{
    /** @test */
    public function make_module_command_works()
    {
        $this->artisan('make:module test')
             ->assertExitCode(0);
    }
}
