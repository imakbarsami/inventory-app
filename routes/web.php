<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


    //sale routes
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');      
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create'); 
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');     
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');  

    //dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //report route
    Route::get('/reports/financial', [ReportController::class, 'financialReport'])->name('reports.financial');

});

require __DIR__.'/auth.php';
