<?php

// config for Mbedzinski/ClassName
return [
    
    'structure' => 'default',
    
    'structures' => [
        'default' => [
            'baseDir' => '\app\Modules',
            'baseNamespace' => 'App\Modules',
            'paths'   => [
                'providers' => '{base_dir}\ServiceProviders',
                'models'           => '{base_dir}\Models',
                'config'           => '{base_dir}\Config',
                'controllers'      => '{base_dir}\Controllers',
                'views'            => '{base_dir}\Views',
                'routes'           => '{base_dir}\Routes',
                'migrations'       => '{base_dir}\Database\Migrations',
                'requests'         => '{base_dir}\Requests',
                'helpers'          => '{base_dir}\Helpers',
                'services'         => '{base_dir}\Services',
            ],
        ],
    ],

];
