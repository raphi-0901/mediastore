<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
if (env('APP_ENV') === 'production')
    URL::forceScheme('https');

Auth::routes([
    'verify' => false,
    'reset' => true,
    'register' => false
]);

//this makes it easy to test if server has the right env variables..
//disable before production usage
Route::get('env/{key}', function ($key) {
    return env($key);
});

Route::group(['middleware' => ['auth']],
    function () {
        Route::get('/', 'HomeController@index')->name('index');
        Route::get('/index', 'HomeController@index');
        Route::get('/home', 'HomeController@index')->name('home');

        //Bestellungen
        Route::name('orders.')->prefix('orders')->group(function () {
            Route::get('/', 'OrderController@index')->name("index");
            Route::get('/filter', 'OrderController@filter')->name("filter");

            Route::get('{id}', 'OrderController@show')->name("show");
            Route::post('setup', 'OrderController@setup')->name("setup");
            Route::delete('destroy/{id}', 'OrderController@destroy')->name("destroy");
            Route::post('finish', 'OrderController@finish')->name("finish");

            //sets the givenBy and picked_at values in orders table
            Route::get('pick/{id}', 'OrderController@pick')->name("pick");

            //sets the returnedBy and returned_at values in orders table
            Route::get('return/{id}', 'OrderController@return')->name("return");

            //teacher can accept or deny an order
            Route::get('accept/{id}', 'OrderController@accept')->name("accept");
            Route::post('deny/{id}', 'OrderController@deny')->name("deny");

            //teacher scans back and out the devices. then the fields in the device_order are set.
            Route::post('/handleQRCodeScan', 'OrderController@handleQRCodeScan')->name("handleQRCodeScan");
            Route::post('/undoScan', 'OrderController@undoScan')->name("undoScan");

            //teacher or admin can remove device from order.
            Route::post('/removeDevice', 'OrderController@removeDevice')->name("removeDevice");
        });

        //Benutzer
        Route::name('users.')->prefix('users')->group(function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::get('/{id}', 'UserController@show')->name('show');
            Route::patch('update/{id}', 'UserController@update')->name('update');
            Route::delete('destroy/{id}', 'UserController@destroy')->name('destroy');
            Route::get('/deleted', 'UserController@deletedUsers')->name('deletedUsers');
            Route::patch('restore/{id}', 'UserController@restore')->name('restore');
            Route::delete('forceDelete/{id}', 'UserController@forceDelete')->name('forceDelete');
            Route::post('setDates', 'UserController@storeDates')->name("setDates");
        });

        //Warenkorb
        Route::name('shoppingCart.')->prefix('shoppingCart')->group(function () {
            Route::get('/', 'UserController@showShoppingCart')->name('show');
            Route::post('add', 'UserController@addToShoppingCart')->name('add');
            Route::post('remove', 'UserController@removeFromShoppingCart')->name('remove');
            Route::get('clear', 'UserController@clearShoppingCart')->name('clear');
        });

        //Einstellungen
        Route::name('settings')->prefix("settings")->group(function () {
            Route::get('/', 'SettingController@getSettingsView');
            Route::post('/', 'SettingController@update');
        });

        //Geräte
        Route::name('devices.')->prefix('devices')->group(function () {
            Route::get('/', 'DeviceController@index')->name("index");
            Route::get('/filter', 'DeviceController@filter')->name("filter");
            Route::post('/store', 'DeviceController@store')->name("store");
            Route::patch('/update/{id}', 'DeviceController@update')->name("update");
            Route::get('{id}', 'DeviceController@show')->name("show");
            Route::post('import', 'DeviceController@createFromExcel')->name("import");
            Route::post('setup', 'DeviceController@setup')->name("setup");
            Route::post('pickUp', 'DeviceController@pickUp')->name("pickUp");

            Route::delete('destroy/{id}', 'DeviceController@destroy')->name('destroy');
            Route::get('/deleted', 'DeviceController@deletedDevices')->name('deletedDevices');
            Route::patch('restore/{id}', 'DeviceController@restore')->name('restore');
            Route::delete('forceDelete/{id}', 'DeviceController@forceDelete')->name('forceDelete');

            //download qrcode
            Route::get('/download/qrcodes/{id}', 'DeviceController@downloadQRCode')->name('downloadQRCode');
            Route::get('/download/qrcodes', 'DeviceController@downloadQRCodes')->name('downloadQRCodes');
        });

        //Kategorien
        Route::name('types.')->prefix('types')->group(function () {
            Route::get('/', 'TypeController@index')->name("index");
            Route::post('/store', 'TypeController@store')->name("store");
            Route::patch('/update/{id}', 'TypeController@update')->name("update");
            Route::delete('destroy/{id}', 'TypeController@destroy')->name('destroy');
        });
    });
//policies
Route::get('policies', 'HomeController@showPolicies') -> name("policies");

//impressum
Route::get('impressum', 'HomeController@showImpressum') -> name("impressum");

//azure
Route::get('login/azure', 'LoginController@redirectToProvider')->name("login.azure");
Route::get('logout/azure', 'LoginController@logout')->name("logout.azure");
Route::get('login/azure/callback', 'LoginController@handleProviderCallback');

//testroutes for differen users
/*
Route::name('login.')->prefix('login')->group(function () {
    Route::get('student', function () {
        $user = \App\User::where("role_id", "=", 3)->get()->random();
        Auth::login($user);
        return redirect(route("index"));
    })->name('student');

    Route::get('teacher', function () {
        $user = \App\User::where("role_id", "=", 2)->get()->random();
        Auth::login($user);
        return redirect(route("index"));
    })->name('teacher');

    Route::get('admin', function () {
        $user = \App\User::where("role_id", "=", 1)->get()->random();
        Auth::login($user);
        return redirect(route("index"));
    })->name('admin');

    Route::get('{id}', function ($id) {
        Auth::loginUsingId($id);
        return redirect(route("index"));
    })->name('admin');
});
*/
