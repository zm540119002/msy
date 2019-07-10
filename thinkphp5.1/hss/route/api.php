<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/10
 * Time: 9:41
 */
Route::get('new', function () {
    return 'hello,55555555!';
});
Route::post('new/:id','api/Test/index');