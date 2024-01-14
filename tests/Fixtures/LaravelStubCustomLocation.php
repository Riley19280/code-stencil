<?php

use CodeStencil\Stencil;

return Stencil::make()
    ->php()
    ->namespace('App\Models')
    ->use('Illuminate\Database\Eloquent\Factories\HasFactory')
    ->use('Illuminate\Database\Eloquent\Model')
    ->curlyStatement('class i_name extends Model', fn(Stencil $s) => $s
        ->line('use HasFactory;')
    )
    ->overrideStubLocation(__DIR__ . DIRECTORY_SEPARATOR . 'Output.php');
