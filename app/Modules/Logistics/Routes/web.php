<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Logistics\Controllers\LogisticsAjaxController;

Route::middleware(['auth', 'role:admin,mitra'])->group(function () {
    Route::get('/logistics', [LogisticsAjaxController::class, 'index'])->name('logistics.index');
    Route::get('/logistics/api/data', [LogisticsAjaxController::class, 'getData'])->name('logistics.api.data');
    Route::post('/logistics/api/store', [LogisticsAjaxController::class, 'store'])->name('logistics.api.store');
    Route::get('/logistics/api/{id}', [LogisticsAjaxController::class, 'show'])->name('logistics.api.show');
    Route::post('/logistics/api/{id}/update', [LogisticsAjaxController::class, 'update'])->name('logistics.api.update');
    Route::delete('/logistics/api/{id}', [LogisticsAjaxController::class, 'destroy'])->name('logistics.api.destroy');
    Route::get('/logistics/{id}/download', [LogisticsAjaxController::class, 'downloadDocument'])->name('logistics.download');
});

