<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'admin','middleware'=>'auth.checkrole:admin','as'=>'admin.'],function () {

    //routes category
    Route::get('categories', ['as' => 'categories.index', 'uses' => 'CategoriesController@index']);
    Route::get('categories/create', ['as' => 'categories.create', 'uses' => 'CategoriesController@create']);
    Route::get('categories/edit/{id}', ['as' => 'categories.edit', 'uses' => 'CategoriesController@edit']);
    Route::post('categories/update/{id}', ['as' => 'categories.update', 'uses' => 'CategoriesController@update']);
    Route::post('categories/store', ['as' => 'categories.store', 'uses' => 'CategoriesController@store']);
    //routes client
    Route::get('clients', ['as' => 'clients.index', 'uses' => 'ClientsController@index']);
    Route::get('clients/create', ['as' => 'clients.create', 'uses' => 'ClientsController@create']);
    Route::get('clients/edit/{id}', ['as' => 'clients.edit', 'uses' => 'ClientsController@edit']);
    Route::post('clients/update/{id}', ['as' => 'clients.update', 'uses' => 'ClientsController@update']);
    Route::post('clients/store', ['as' => 'clients.store', 'uses' => 'ClientsController@store']);

    //products
    Route::get('products', ['as' => 'products.index', 'uses' => 'ProductsController@index']);
    Route::get('products/create', ['as' => 'products.create', 'uses' => 'ProductsController@create']);
    Route::get('products/edit/{id}', ['as' => 'products.edit', 'uses' => 'ProductsController@edit']);
    Route::get('products/destroy/{id}', ['as' => 'products.destroy', 'uses' => 'ProductsController@destroy']);
    Route::post('products/update/{id}', ['as' => 'products.update', 'uses' => 'ProductsController@update']);
    Route::post('products/store', ['as' => 'products.store', 'uses' => 'ProductsController@store']);
    //orders
    Route::get('orders', ['as' => 'orders.index', 'uses' => 'OrdersController@index']);
    Route::get('orders/{id}', ['as' => 'orders.edit', 'uses' => 'OrdersController@edit']);
    Route::post('orders/update/{id}', ['as' => 'orders.update', 'uses' => 'OrdersController@update']);
    //cupoms
    Route::get('cupoms/create', ['as' => 'cupoms.create', 'uses' => 'CupomsController@create']);
    Route::get('cupoms', ['as' => 'cupoms.index', 'uses' => 'CupomsController@index']);
    Route::get('cupoms/{id}', ['as' => 'cupoms.edit', 'uses' => 'CupomsController@edit']);
    Route::post('cupoms/update/{id}', ['as' => 'cupoms.update', 'uses' => 'CupomsController@update']);
    Route::post('cupoms/store', ['as' => 'cupoms.store', 'uses' => 'CupomsController@store']);
});

Route::group(['prefix'=>'customer','middleware'=>'auth.checkrole:client', 'as'=>'customer.'],function (){


    Route::get('order',['as'=>'order.index','uses'=>'CheckoutController@index']);
    Route::post('order/store',['as'=>'order.store','uses'=>'CheckoutController@store']);
    Route::get('order/create',['as'=>'order.create','uses'=>'CheckoutController@create']);
});

Route::group(['middleware'=>'cors'],function (){
    Route::post('oauth/access_token', function() {
        return Response::json(Authorizer::issueAccessToken());
    });

    Route::group(['prefix'=>'api','middleware'=>'oauth','as'=>'api.'],function () {

        Route::group(['prefix'=>'client','middleware'=>'oauth.checkrole:client','as'=>'client.'],function () {
            Route::resource('order','Api\Client\ClientCheckoutController',
                ['except'=>['create','edit','delete']]
            );
            Route::get('product','Api\Client\ClientProductController@index');
        });

        Route::group(['prefix'=>'deliveryman','middleware'=>'oauth.checkrole:deliveryman','as'=>'deliveryman.'],function () {
            Route::resource('order',
                'Api\Deliveryman\DeliverymanCheckoutController',
                ['except'=>['create','edit','delete','store']]
            );

            Route::patch('order/{id}/update-status',
                ['as'=>'order.update-status',
                    'uses'=>'Api\Deliveryman\DeliverymanCheckoutController@updateStatus']
            );
            Route::patch('order/{id}/close-order',
                ['as'=>'order.close-order',
                    'uses'=>'Api\Deliveryman\DeliverymanCheckoutController@closeOder']
            );
            Route::post('order/{id}/geo',[
                'as'=>'orders.geo','uses'=>'Api\Deliveryman\DeliverymanCheckoutController@geo'
            ]);

            Route::get('order/iniciada/{id}',[
                'as'=>'order.iniciada', 'uses'=>'Api\Deliveryman\DeliverymanCheckoutController@getIniciada']);

            Route::get('auxiliary','Api\Deliveryman\DeliverymanAuxiliaryController@index');

            Route::get('count','Api\Deliveryman\DeliverymanCheckoutController@count');

            Route::get('countAnt','Api\Deliveryman\DeliverymanCheckoutController@countAnt');

            Route::get('countT','Api\Deliveryman\DeliverymanCheckoutController@countT');

            Route::get('countAntT','Api\Deliveryman\DeliverymanCheckoutController@countAntT');
        });

        Route::get('authenticated','Api\UserController@authenticated');
        Route::patch('device_token','Api\UserController@updateDeviceToken');
        Route::get('cupom/{code}','Api\CupomController@show');

    });
});

