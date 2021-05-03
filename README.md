# This is my package LaravelModuleBuilder

# Usage

### Make module

```php
  php artisan make:module {ModuleName}
```

### Make module model

```
  php artisan make:module -m {ModuleName} {ModelName} 

  -a,    Generate a migration, seeder, factory, and resource controller for the model
  -c,    Create a new controller for the model
  -f,    Create a new factory for the model
  -m,    Create a new migration file for the model
  -s,    Create a new seeder file for the model
  -p,    Indicates if the generated model should be a custom intermediate table model
  -r,    Indicates if the generated controller should be a resource controller
  --api  Indicates if the generated controller should be an API controller
```
