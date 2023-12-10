<?php

use App\Http\Controllers\LiveController;
use App\Http\Controllers\WhatIf\WhatIfController;
use Illuminate\Support\Facades\Route;



function wir()
{
    /* LIVE VIEWING */

    Route::get('', [WhatIfController::class, 'index'])->name('whatif');

    Route::post('cas', [WhatIfController::class, 'cloneAndStart'])->name('whatif.clone');

    Route::group(['middleware' => ['whatif']], function () {

        Route::post('resume', [WhatIfController::class, 'resume'])->name('whatif.resume');

        Route::prefix('editor')->group(function () {
            Route::get('', [WhatIfController::class, 'editorIndex'])->name('whatif.editor');
            Route::get('results/{schema}', [WhatIfController::class, 'editorResults'])->name('whatif.editor.results');
            Route::post('userc', [WhatIfController::class, 'updateSercResult'])->name('whatif.userc');
        });
    });
}

if (env('APP_ENV') == 'local') {
    Route::prefix('whatif')->group(function () {
        wir();
    });
} else {

    Route::domain('whatif.bulsca.co.uk')->group(function () {
        wir();
    });
}
