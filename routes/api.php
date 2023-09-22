<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnyOneController;

Route::controller(AnyOneController::class)
    ->group(function () {
        Route::get('/get-new/{new}', 'getNew');
        Route::get('/get-all-news', 'getAllNews');
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
        Route::get('/get-all-courses', 'getAllCourses');
        Route::post('/edit-course/{course}', 'editCourse');
        Route::post('/delete-course/{course}', 'deleteCourse');
        Route::get('/get-course-videos/{course}', 'getCourseVideos');

        // Chapters
        Route::post('/create-chapter', 'createChapter');
        Route::get('/get-all-chapters', 'getAllChapters');
        Route::post('/edit-chapter/{chapter}', 'editChapter');
        Route::post('/delete-chapter/{chapter}', 'deleteChapter');
        Route::get('/get-course-chapters/{course}', 'getCourseChapters');
    });

require __DIR__ . '/auth.php';
