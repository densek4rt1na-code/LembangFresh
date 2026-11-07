<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $modulesPath = app_path('Modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $module) {
                $moduleName = basename($module);
                $lowerName = strtolower($moduleName);

                $routesPath = $module . '/Routes/web.php';
                if (File::exists($routesPath)) {
                    Route::middleware('web')
                        ->group($routesPath);
                }

                $viewsPath = $module . '/Views';
                if (File::isDirectory($viewsPath)) {
                    View::addNamespace($lowerName, $viewsPath);
                }
            }
        }
    }
}

