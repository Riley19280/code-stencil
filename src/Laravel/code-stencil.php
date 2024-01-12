<?php

return [
    /**
     * If compilation is enabled, then any new stencil files created by commands will be processed
     */
    'enable-compilation' => true,

    /**
     * Ignore specific directories, files, or patterns from being processed
     */
    'ignore'             => [
        // directories should be relative to the application root.
        'directories' => [
            'vendor',
            'node_modules',
            '.git',
            'storage',
            'public',
        ],
        // File names, Ex. foo.txt
        'files'       => [],
        // Patterns are matched against the full file path of files
        'patterns'    => [],
    ],
];
