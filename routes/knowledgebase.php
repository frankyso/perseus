<?php

use App\Http\Controllers\Knowledgebase\KnowledgebaseController;
use Illuminate\Support\Facades\Route;

Route::get('knowledgebase', [KnowledgebaseController::class, 'index'])->name('knowledgebase.index');
Route::get('knowledgebase/search', [KnowledgebaseController::class, 'search'])->name('knowledgebase.search');
Route::get('knowledgebase/{category:slug}', [KnowledgebaseController::class, 'category'])->name('knowledgebase.category');
Route::get('knowledgebase/{category:slug}/{article:slug}', [KnowledgebaseController::class, 'show'])->name('knowledgebase.show');
