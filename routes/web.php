<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubSystemController;
use App\Http\Controllers\SiteDocumentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-info', [DashboardController::class, 'updateInfo'])->name('dashboard.update.info')->middleware('admin');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('admin')->group(function () {
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('projects/{project}/files', [ProjectFileController::class, 'index'])->name('projects.files.index');
        Route::post('projects/{project}/files', [ProjectFileController::class, 'store'])->name('projects.files.store');
        Route::delete('projects/{project}/files/{projectFile}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');
    });

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    
    Route::get('projects/{project}/completion-report', [ProjectController::class, 'completionReport'])->name('projects.completion_report');

    Route::post('projects/{project}/sub-systems', [SubSystemController::class, 'store'])->name('sub_systems.store')->middleware('admin');
    Route::get('sub-systems/{subSystem}', [SubSystemController::class, 'show'])->name('sub_systems.show');
    Route::middleware('admin')->group(function () {
        Route::get('sub-systems/{subSystem}/edit', [SubSystemController::class, 'edit'])->name('sub_systems.edit');
        Route::put('sub-systems/{subSystem}', [SubSystemController::class, 'update'])->name('sub_systems.update');
        Route::delete('sub-systems/{subSystem}', [SubSystemController::class, 'destroy'])->name('sub_systems.destroy');
    });

    Route::get('sites/{site}', [SiteController::class, 'show'])->name('sites.show');

    // Site Admin Actions
    Route::middleware('admin')->group(function () {
        Route::post('sub-systems/{subSystem}/sites', [SiteController::class, 'store'])->name('sites.store');
        Route::get('sites/{site}/edit', [SiteController::class, 'edit'])->name('sites.edit');
        Route::put('sites/{site}', [SiteController::class, 'update'])->name('sites.update');
        Route::delete('sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
    });

    Route::post('sites/{site}/documents', [SiteDocumentController::class, 'store'])->name('sites.documents.store');
    Route::get('site-documents/{siteDocument}/download', [SiteDocumentController::class, 'download'])->name('site-documents.download');
    Route::get('site-documents/{siteDocument}/view', [SiteDocumentController::class, 'view'])->name('site-documents.view');
    Route::delete('site-documents/{siteDocument}', [SiteDocumentController::class, 'destroy'])->name('site-documents.destroy');

    Route::post('sites/{site}/comments', [CommentController::class, 'store'])->name('sites.comments.store');

    Route::get('download-files', [ProjectFileController::class, 'allProjectFiles'])->name('project-files.all');
    Route::get('project-files/{projectFile}/download', [ProjectFileController::class, 'download'])->name('project-files.download');
});

require __DIR__.'/auth.php';
