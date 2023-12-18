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
            Route::get('results/speeds/{speed}', [WhatIfController::class, 'getSpeedResults'])->name('whatif.editor.speeds');
            Route::get('results/sercs/{serc}', [WhatIfController::class, 'getSercResults'])->name('whatif.editor.sercs');
            Route::get('results/{schema}', [WhatIfController::class, 'editorResults'])->name('whatif.editor.results');

            Route::post('userc', [WhatIfController::class, 'updateSercResult'])->name('whatif.userc');
            Route::post('uspeed', [WhatIfController::class, 'updateSpeedResult'])->name('whatif.uspeed');
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
