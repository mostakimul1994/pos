<?php

/*
|--------------------------------------------------------------------------
| Installation Web Routes
|--------------------------------------------------------------------------
|
| Routes related to installation of the software
|
*/

Route::get('/install', 'Install\InstallController@index')->name('install.index');
Route::get('/install/check-server', 'Install\InstallController@checkServer')->name('install.checkServer');
Route::get('/install/details', 'Install\InstallController@details')->name('install.details');
Route::post('/install/post-details', 'Install\InstallController@postDetails')->name('install.postDetails');
Route::post('/install/install-alternate', 'Install\InstallController@installAlternate')->name('install.installAlternate');
Route::get('/install/success', 'Install\InstallController@success')->name('install.success');

Route::get('/install/update/{version?}', 'Install\InstallController@update')->name('install.update');
