<?php
use Illuminate\Support\Facades\Mail;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/'], function ($app) {


    $app->post('userlogin/','UsersController@userlogin');
    $app->post('usersignup/','UsersController@usersignup');
    $app->post('userforgetPassword/','UsersController@userforgetPassword');
    $app->post('userresetPassword/','UsersController@userresetPassword');
    $app->post('userupdateProfile/','UserActivityController@userupdateProfile');



    $app->post('userdetailstest/','UsersController@userdetails');


    $app->post('userdetails','TodoController@userdetails');

});


$router->get('sendmail', function () use ($router) {
    Mail::raw('Raw string email', function($msg) {
        $msg->to(['engr.laravel@gmail.com']);
        $msg->from(['shafiq@dubaisoftwaresolutions.com']);
    });

    $to      = 'engr.laravel@gmail.com';
    $subject = 'the subject';
    $message = 'hello';
    $headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);

    //Mail::to('engr.laravel@gmail.com')->send();

});
