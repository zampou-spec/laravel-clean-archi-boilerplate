<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnyOneController;

Route::controller(AnyOneController::class)
    ->group(function () {
        Route::get('/get-news/{news}', 'getNews');
        Route::get('/get-all-news', 'getAllNews');
        Route::get('/get-all-courses', 'getAllCourses');
        Route::get('/get-all-products', 'getAllProducts');
    });

Route::controller(UserController::class)
    ->group(function () {
        Route::get('/get-subscribes', 'getSubscribes');
        Route::get('/get-subscribe-courses', 'getSubscribeCourses');
        Route::post('/make-payment/{course}/{subscribe_type}', 'makePayment');
    });

Route::controller(AdminController::class)
    ->group(function () {
        // Users
        Route::get('/get-all-user', 'getAllUser');
        Route::get('/get-statistics', 'getStatistics');
        Route::post('/remove-sold/{user}', 'removeSold');

        // Courses
        Route::post('/create-course', 'createCourse');
        Route::get('/get-course/{course}', 'getCourse');
        Route::post('/edit-course/{course}', 'editCourse');
        Route::post('/delete-course/{course}', 'deleteCourse');
        Route::get('/get-course-videos/{course}', 'getCourseVideos');

        // Chapters
        Route::post('/create-chapter', 'createChapter');
        Route::get('/get-all-chapters', 'getAllChapters');
        Route::post('/edit-chapter/{chapter}', 'editChapter');
        Route::post('/delete-chapter/{chapter}', 'deleteChapter');
        Route::get('/get-course-chapters/{course}', 'getCourseChapters');

        // News
        Route::post('/create-news', 'createNews');
        Route::post('/edit-news/{news}', 'editNews');
        Route::post('/delete-news/{news}', 'deleteNews');

        // Products
        Route::post('/create-product', 'createProduct');
        Route::post('/edit-product/{product}', 'editProduct');
        Route::post('/delete-product/{product}', 'deleteProduct');
    });

require __DIR__ . '/auth.php';
