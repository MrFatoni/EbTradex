<?php
//User Group Role
Route::resource('user-role-managements', 'Core\UserRoleManagementsController')->parameter('user-role-managements','id')->except(['show']);
Route::put('user-role-managements/{id}/change-status', 'Core\UserRoleManagementsController@changeStatus')->name('user-role-managements.status');
//User Managements
Route::get('users/{id}/edit/status', 'User\Admin\UsersController@editStatus')->name('users.edit.status');
Route::put('users/{id}/update/status', 'User\Admin\UsersController@updateStatus')->name('users.update.status');
Route::resource('users', 'User\Admin\UsersController')->parameter('users','id');
//User profile
Route::get('profile', 'User\ProfileController@index')->name('profile.index');
Route::get('profile/edit', 'User\ProfileController@edit')->name('profile.edit');
Route::put('profile/update', 'User\ProfileController@update')->name('profile.update');
Route::get('profile/change-password', 'User\ProfileController@changePassword')->name('profile.change-password');
Route::put('profile/change-password/update', 'User\ProfileController@updatePassword')->name('profile.update-password');
Route::get('profile/setting','User\ProfileController@setting')->name('profile.setting');
Route::get('profile/setting/edit','User\ProfileController@settingEdit')->name('profile.setting.edit');
Route::put('profile/setting','User\ProfileController@settingUpdate')->name('profile.setting');
Route::get('profile/avatar/edit','User\ProfileController@avatarEdit')->name('profile.avatar.edit');
Route::put('profile/avatar/update','User\ProfileController@avatarUpdate')->name('profile.avatar.update');

//Admin Setting
Route::get('admin-settings/{admin_setting_type?}', 'Core\AdminSettingController@index')->name('admin-settings.index');
Route::get('admin-settings/{admin_setting_type}/edit', 'Core\AdminSettingController@edit')->name('admin-settings.edit');
Route::put('admin-settings/{admin_setting_type}/update', 'Core\AdminSettingController@update')->name('admin-settings.update');

//Admin Notice
Route::resource('system-notices', 'Core\SystemNoticeController')->except(['show'])->parameter('system-notices','id');

//User Specific Notice
Route::get('notices','User\NotificationController@index')->name('notices.index');
Route::get('notices/{id}/read','User\NotificationController@markAsRead')->name('notices.mark-as-read');
Route::get('notices/{id}/unread','User\NotificationController@markAsUnread')->name('notices.mark-as-unread');

//Laravel Log Viewer
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('logs.index');

//Laravel Audits
Route::get('audits', 'Core\AuditsController@index')->name('audits.index');

//Ajax route
//Route::post('get-modules', 'Core\DropdownController@getModules');
Route::get('logout', 'Guest\AuthController@logout')->name('logout');

Route::get('menu-manager/{menu_slug?}', 'Core\NavController@index')->name('menu-manager.index');
Route::post('menu-manager/{menu_slug?}/save', 'Core\NavController@save')->name('menu-manager.save');