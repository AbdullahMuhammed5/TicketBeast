<?php

Route::get('concerts/{id}', 'ConcertController@show');

Route::post('concerts/{id}/orders', 'ConcertOrdersController@store');
