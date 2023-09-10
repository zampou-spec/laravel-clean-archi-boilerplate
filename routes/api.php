<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

//Route::controller(\App\Http\Controllers\Api\UserController::class)
//    ->group(function () {});

Route::controller(AdminController::class)
    ->group(function () {
        Route::get('/get-all-user', 'getAllUser');
        Route::get('/get-statistics', 'getStatistics');
        Route::post('/remove-sold/{user}', 'removeSold');

        // Courses
        Route::post('/create-course', 'createCourse');
        Route::get('/get-all-courses', 'getAllCourses');
        Route::post('/edit-course/{course}', 'editCourse');
        Route::post('/delete-course/{course}', 'deleteCourse');
        Route::get('/get-course-videos/{course}/{user?}', 'getCourseVideos');

        // Chapters
        Route::post('/create-chapter', 'createChapter');
        Route::get('/get-all-chapters', 'getAllChapters');
        Route::post('/edit-chapter/{chapter}', 'editChapter');
        Route::post('/delete-chapter/{chapter}', 'deleteChapter');
    });

require __DIR__ . '/auth.php';
