<?php

declare(strict_types=1);

use App\AutoPuml\PlantUmlConverter;
use App\AutoPuml\DependencySearcher;
use Illuminate\Support\Facades\Route;
use Flashcard\Infrastructure\Http\Controllers\v2\SessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (DependencySearcher $searcher, PlantUmlConverter $converter) {
    $map = $searcher->findRelatedClasses(SessionController::class, 1);

    dump($converter->convert($map));
});
