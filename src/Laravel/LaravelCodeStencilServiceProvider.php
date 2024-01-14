<?php

namespace CodeStencil\Laravel;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class LaravelCodeStencilServiceProvider extends ServiceProvider
{
    use RegistersOverrideStubLocationMacro;

    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/code-stencil.php' => config_path('code-stencil.php'),
        ]);

        if (config('code-stencil.enable-compilation')) {
            Event::listen(function(CommandStarting $commandStarting) {
                App::instance('command-file-list', $this->getFiles());
            });

            Event::listen(function(CommandFinished $commandFinished) {
                $previousFiles = App::make('command-file-list');

                $currentFiles = $this->getFiles();

                $newFiles = array_values(array_diff($currentFiles, $previousFiles));

                $availableArgs = [
                    ...$commandFinished->input->getArguments(),
                    ...$commandFinished->input->getOptions(),
                ];

                $prefixedArgs = [];

                foreach ($availableArgs as $arg => $val) {
                    $prefixedArgs['i_' . $arg] = $val;
                }

                foreach ($newFiles as $newFile) {
                    (new StencilFileProcessor($newFile, $prefixedArgs))();
                }

                App::forgetInstance('command-file-list');
            });
        }

        $this->registerOverrideStubLocationMacro();
    }

    private function getFiles(): array
    {
        $dirIter    = new RecursiveDirectoryIterator(base_path());
        $filterIter = new RecursiveCallbackFilterIterator($dirIter, function(SplFileInfo $file) use (&$i) {
            if ($file->isDir()) {
                $baseDirectoryPath = trim(str_replace(base_path(), '', $file->getPathname()), '/');

                foreach (config('code-stencil.ignore.directories') as $dir) {
                    if ($dir === $baseDirectoryPath) {
                        return false;
                    }
                }
            } else {
                foreach (config('code-stencil.ignore.files') as $fileName) {
                    if ($file->getFilename() === $fileName) {
                        return false;
                    }
                }

                foreach (config('code-stencil.ignore.patterns') as $pattern) {
                    if (preg_match($pattern, $file->getPath())) {
                        return false;
                    }
                }

            }

            return true;
        });

        $rii = new RecursiveIteratorIterator($filterIter);

        $files = [];
        foreach ($rii as $file) {
            if (!$file->isDir()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
