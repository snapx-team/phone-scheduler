<?php


Route::group(['middleware' => ['web', 'phone_scheduler_role_check']], function () {
    Route::group(['namespace' => 'Xguard\PhoneScheduler\Http\Controllers',], function () {
        Route::group(['prefix' => 'phone-scheduler',], function () {

            // We'll let vue router handle 404 (it will redirect to dashboard)
            Route::fallback('PhoneScheduleController@getIndex');

            // All view routes are handled by vue router
            Route::get('/', 'PhoneScheduleController@getIndex');
            Route::get('/dashboard', 'PhoneScheduleController@getIndex');
            Route::get('/phoneline', 'PhoneScheduleController@getIndex');

            // Phone Schedule App Data
            Route::get('/get-phone-line-data/{id}', 'PhoneScheduleController@getPhoneLineData');
            Route::get('/get-dashboard-data', 'PhoneScheduleController@getDashboardData');

            // Phone Lines
            Route::post('/create-phone-line', 'PhoneLineController@createPhoneLine');
            Route::post('/delete-phone-line/{id}', 'PhoneLineController@deletePhoneLine');
            Route::get('/get-phone-lines', 'PhoneLineController@getPhoneLines');
            Route::get('/get-tags', 'PhoneLineController@getTags');
            Route::post('/update-mode-data', 'PhoneLineController@updateModeData');

            // Columns
            Route::post('/create-columns', 'ColumnController@createOrUpdateColumns');

            // Employee Cards
            Route::post('/create-employee-cards', 'EmployeeCardController@createEmployeeCards');
            Route::post('/get-employee-cards-by-column/{id}', 'EmployeeCardController@getEmployeeCardsByColumn');
            Route::post('/update-employee-card-indexes', 'EmployeeCardController@updateEmployeeCardIndexes');
            Route::post('/update-employee-card-column/{columnId}/{employeeCardId}', 'EmployeeCardController@updateEmployeeCardColumnId');

            Route::post('/delete-employee-card/{id}', 'EmployeeCardController@deleteEmployeeCard');

            // Employees
            Route::post('/create-employee', 'EmployeeController@createEmployee');
            Route::post('/delete-employee/{id}', 'EmployeeController@deleteEmployee');
            Route::get('/get-employees', 'EmployeeController@getEmployees');

            // Phone Line Members
            Route::post('/create-members/{id}', 'MemberController@createMembers');
            Route::post('/delete-member/{id}', 'MemberController@deleteMember');
            Route::get('/get-members/{id}', 'MemberController@getMembers');
        });
    });
});

// API

Route::group(['namespace' => 'Xguard\PhoneScheduler\Http\Controllers',], function () {
    Route::group(['prefix' => 'phone-scheduler',], function () {

        Route::get('/api/formatted-phone-line-data/{id}', 'PhoneScheduleController@getFormattedData');
        Route::get('/api/get-available-agent/{id}/{level}', 'PhoneScheduleController@getAvailableAgent');
        Route::get('/api/get-recent-caller-info', 'PhoneScheduleController@getRecentCallerInfo');
        Route::post('/api/log-call', 'PhoneScheduleController@logCall');
        Route::get('/api/get-directory-data/{id}', 'PhoneScheduleController@getDirectoryData');
    });
});
