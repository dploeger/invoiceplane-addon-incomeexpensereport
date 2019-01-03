<?php

Route::group(['prefix' => 'addons/report', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'Addons\IncomeExpenseReport\Controllers'], function () {
    Route::get('income_expense', ['uses' => 'IncomeExpenseController@index', 'as' => 'addons.reports.incomeexpensereport.options']);
    Route::post('income_expense/validate', ['uses' => 'IncomeExpenseController@validateOptions', 'as' => 'addons.reports.incomeexpensereport.validate']);
    Route::get('income_expense/html', ['uses' => 'IncomeExpenseController@html', 'as' => 'addons.reports.incomeexpensereport.html']);
    Route::get('income_expense/pdf', ['uses' => 'IncomeExpenseController@pdf', 'as' => 'addons.reports.incomeexpensereport.pdf']);
});