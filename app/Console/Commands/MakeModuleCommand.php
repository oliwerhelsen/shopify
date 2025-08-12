<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module}';
    protected $description = 'Create a new module with the standard structure';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $modulePath = app_path("Modules/{$name}");

        if ($this->files->exists($modulePath)) {
            $this->error("Module {$name} already exists!");
            return 1;
        }

        $this->createDirectoryStructure($name, $modulePath);
        $this->createFiles($name, $modulePath);

        $this->info("Module {$name} created successfully!");
        return 0;
    }

    protected function createDirectoryStructure($name, $modulePath)
    {
        $directories = [
            'Application/Actions',
            'Application/DTO',
            'Application/Queries',
            'Application/Services',
            'Domain/Contracts',
            'Domain/Events',
            'Domain/Models',
            'Domain/Policies',
            'Domain/ValueObjects',
            'Http/Controllers',
            'Http/Requests',
            'Http/Resources',
            'Infrastructure/Persistence/Eloquent',
            'Infrastructure/Persistence/Migrations',
            'Infrastructure/Providers',
            'Routes',
            'Tests'
        ];

        foreach ($directories as $directory) {
            $this->files->makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }
    }

    protected function createFiles($name, $modulePath)
    {
        $this->createServiceProvider($name, $modulePath);
        $this->createController($name, $modulePath);
        $this->createModel($name, $modulePath);
        $this->createRoutes($name, $modulePath);
    }

    protected function createServiceProvider($name, $modulePath)
    {
        $stub = "<?php

namespace App\Modules\\{$name}\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \$this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        \$this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
    }
}
";

        $this->files->put("{$modulePath}/Infrastructure/Providers/{$name}ServiceProvider.php", $stub);
    }

    protected function createController($name, $modulePath)
    {
        $stub = "<?php

namespace App\Modules\\{$name}\Http\Controllers;

use App\Http\Controllers\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        //
    }

    public function show(\$id)
    {
        //
    }

    public function store()
    {
        //
    }

    public function update(\$id)
    {
        //
    }

    public function destroy(\$id)
    {
        //
    }
}
";

        $this->files->put("{$modulePath}/Http/Controllers/{$name}Controller.php", $stub);
    }

    protected function createModel($name, $modulePath)
    {
        $stub = "<?php

namespace App\Modules\\{$name}\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$fillable = [];
}
";

        $this->files->put("{$modulePath}/Domain/Models/{$name}.php", $stub);
    }

    protected function createRoutes($name, $modulePath)
    {
        $webStub = "<?php

use App\Modules\\{$name}\Http\Controllers\\{$name}Controller;
use Illuminate\Support\Facades\Route;

Route::resource('" . strtolower($name) . "', {$name}Controller::class);
";

        $apiStub = "<?php

use App\Modules\\{$name}\Http\Controllers\\{$name}Controller;
use Illuminate\Support\Facades\Route;

Route::apiResource('" . strtolower($name) . "', {$name}Controller::class);
";

        $this->files->put("{$modulePath}/Routes/web.php", $webStub);
        $this->files->put("{$modulePath}/Routes/api.php", $apiStub);
    }
}