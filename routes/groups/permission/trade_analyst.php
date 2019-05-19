<?php

Route::group(['prefix' => 'trade-analysis', 'namespace' => 'User\TradeAnalyst'], function () {
    Route::resource('posts', 'PostsController')->except('show')->parameter('posts', 'id')->names('trade-analyst.posts');
    Route::put('posts/{id}/toggle-status', 'PostsController@toggleActiveStatus')->name('trade-analyst.posts.toggle-status');

    Route::get('questions', 'QuestionsController@index')->name('trade-analyst.questions.index');

    Route::get('questions/{id}/answer', 'QuestionsController@answerForm')->name('trade-analyst.questions.answer');

    Route::post('questions/{id}/answer', 'QuestionsController@answer')->name('trade-analyst.questions.answer');

    Route::delete('questions/{id}/destroy', 'QuestionsController@destroy')->name('trade-analyst.questions.destroy');
});