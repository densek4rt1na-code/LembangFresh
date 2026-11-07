<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Harvest\Controllers\HarvestController;

Route::middleware(['auth', 'role:admin,petani'])->group(function () {
    Route::resource('harvests', HarvestController::class);
});

