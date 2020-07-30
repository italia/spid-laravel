<?php
/**
 * This class implements an Artisan command for SPIDAuth Package.
 *
 * This class is a slightly modified version of the
 * Illuminate\Auth\Console\AuthMakeCommand class which is part of the Laravel
 * Framework project and licensed under the MIT license.
 *
 * @license BSD-3-clause
 * @license MIT
 */

namespace Italia\SPIDAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Container\Container;

class CommandExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spid-auth:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provide basic example views, routes and controllers for SPIDAuth Package';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'layouts/app.stub' => 'layouts/app.blade.php',
        'home.stub' => 'home.blade.php',
        'private.stub' => 'private.blade.php',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        file_put_contents(
            app_path('Http/Controllers/HomeController.php'),
            $this->compileStub('controllers/HomeController')
        );

        file_put_contents(
            app_path('Http/Controllers/PrivateController.php'),
            $this->compileStub('controllers/PrivateController')
        );

        file_put_contents(
            app_path('Listeners/SPIDEventSubscriber.php'),
            $this->compileStub('listeners/SPIDEventSubscriber')
        );

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__ . '/stubs/example/routes.stub'),
            FILE_APPEND
        );

        $this->info('SPIDAuth example generated successfully.');
    }

    /**
     * Create the directory for the files.
     */
    protected function createDirectories()
    {
        if (!is_dir($directory = resource_path('views/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = app_path('Listeners'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the example views.
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/' . $value))) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/example/views/' . $key,
                $view
            );
        }
    }

    /**
     * Compiles the stubs.
     *
     * @param string The stub to compile
     *
     * @return string The compiled stub
     */
    protected function compileStub(string $stubName): string
    {
        return str_replace(
            '{{namespace}}',
            Container::getInstance()->getNamespace(),
            file_get_contents(__DIR__ . '/stubs/example/' . $stubName . '.stub')
        );
    }
}
