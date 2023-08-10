<?php

namespace CodeStencil;

class StencilFormatter
{
    protected static $executables = [
        'pint',
        'php-cs-fixer',
        'styleci',
    ];

    protected function getDirectoriesToCheck(): array
    {
        $dirsToCheck = [];

        $dirParts = explode(DIRECTORY_SEPARATOR, __FILE__);

        // Get all subpaths of the current file such that the closest directories are ordered first
        for ($i = count($dirParts) - 1; $i > 0; $i--) {
            $dirsToCheck[] = implode(DIRECTORY_SEPARATOR, [...array_slice($dirParts, 0, $i), 'vendor', 'bin']);
        }

        return $dirsToCheck;
    }

    public function __invoke(string $filepath)
    {
        $formatted = false;

        foreach ($this->getDirectoriesToCheck() as $binPath) {
            if (file_exists($binPath)) {
                foreach (static::$executables as $executable) {
                    $executablePath = implode(DIRECTORY_SEPARATOR, [$binPath, $executable]);
                    if (!$formatted && file_exists($executablePath)) {
                        $this->executeExternalFormatter($filepath, $executablePath);
                        $formatted = true;
                    }
                }
            }
        }
    }

    protected function executeExternalFormatter(string $filepath, string $executablePath): void
    {
        exec("$executablePath $filepath");
    }
}
