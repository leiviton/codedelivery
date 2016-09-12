// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'

angular.module('starter.controllers',[]);
angular.module('starter.services',[]);
angular.module('starter.filters',[]);

angular.module('starter', [
    'ionic','ionic.service.core','starter.controllers','starter.services','starter.filters',
    'angular-oauth2','ngResource','ngCordova','uiGmapgoogle-maps','pusher-angular'
])
    .constant('appConfig',{
        //baseUrl:'http://leiviton.com.br/direta_dev/public',
        baseUrl:'http://192.168.137.201:8000',
        pusherKey: '9da90fc97b93c4ce952a'
    })
    .run(function($ionicPlatform,$window,appConfig,$localStorage) {
        $window.client = new Pusher(appConfig.pusherKey);
        $ionicPlatform.ready(function() {
            if(window.cordova && window.cordova.plugins.Keyboard) {
                // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
                // for form inputs)
                cordova.plugins.Keyboard.hideKeyboardAccessoryBar(false);

                // Don't remove this line unless you know what you are doing. It stops the viewport
                // from snapping when text inputs are focused. Ionic handles this internally for
                // a much nicer keyboard experience.
                cordova.plugins.Keyboard.disableScroll(true);
            }
            if(window.StatusBar) {
                //StatusBar.styleDefault();
                StatusBar.styleBlackTranslucent();
            }
            Ionic.io();
            var push = new Ionic.Push({
                debug:true,
                onNotification: function (message) {
                    console.log(message);
                }
            });
            push.register(function (token) {
                $localStorage.set('device_token',token.token);
            })
        });
    })

    .config(function ($stateProvider, $urlRouterProvider,OAuthProvider,OAuthTokenProvider,appConfig,$provide){
        OAuthProvider.configure({
            baseUrl: appConfig.baseUrl,
            clientId: 'appid01',
            clientSecret: 'secret',
            grantPath: '/oauth/access_token'
        });

        OAuthTokenProvider.configure({
            name: 'token',
            options: {
                secure: false
            }
        });

        $stateProvider

            .state('login',{
                url:'/login',
                templateUrl:'templates/login.html',
                controller:'loginCtrl'
            })
            .state('home',{
                url:'/home',
                templateUrl:'templates/home.html',
                controller:function ($scope) {
                    
                }
            })

            //rotas client
            .state('client',{
                abstract: true,
                cache:false,
                url:'/client',
                templateUrl:'templates/client/menu.html',
                controller: 'ClientMenuCtrl'
            })
            .state('client.checkout',{
                cache: false,
                url:'/checkout',
                templateUrl: 'templates/client/checkout.html',
                controller: 'ClientCheckoutCtrl'
            })
            .state('client.order',{
                url:'/order',
                templateUrl: 'templates/client/order.html',
                controller: 'ClientOrderCtrl'
            })
            .state('client.view_order',{
                url:'/view_order/:id',
                templateUrl: 'templates/client/view_order.html',
                controller: 'ClientViewOrderCtrl'
            })
            .state('client.view_delivery',{
                cache:false,
                url:'/view_delivery/:id',
                templateUrl: 'templates/client/view_delivery.html',
                controller: 'ClientViewDeliveryCtrl'
            })
            .state('client.checkout_item_detail',{
                url:'/checkout/detail/:index',
                templateUrl: 'templates/client/checkout_item_detail.html',
                controller: 'ClientCheckoutDetailCtrl'
            })
            .state('client.checkout_successful',{
                cache: false,
                url:'/checkout/successful',
                templateUrl:'templates/client/checkout_successful.html',
                controller:'ClientCheckoutSuccessful'
            })
            .state('client.view_products',{
                cache:false,
                url:'/view_products',
                templateUrl: 'templates/client/view_products.html',
                controller: 'ClientViewProductCtrl'
            })
            //rotas deliveryman
            .state('deliveryman',{
                abstract: true,
                cache:false,
                url:'/deliveryman',
                templateUrl:'templates/deliveryman/menu.html',
                controller: 'DeliverymanMenuCtrl'
            })
            .state('deliveryman.home',{
                url:'/home',
                templateUrl:'templates/deliveryman/home.html',
                controller:function ($scope) {}
            })
            .state('deliveryman.notification',{
                url:'/notification',
                templateUrl:'templates/deliveryman/notification.html',
                controller:'DeliverymanNotificationCtrl'
            })
            .state('deliveryman.order',{
                cache:false,
                url:'/order',
                templateUrl: 'templates/deliveryman/order.html',
                controller: 'DeliverymanOrderCtrl'
            })
            .state('deliveryman.view_order',{
                url:'/view_order/:id',
                templateUrl: 'templates/deliveryman/view_order.html',
                controller: 'DeliverymanViewOrderCtrl'
            })
            .state('deliveryman.view_close',{
                url:'/view_close/:id',
                templateUrl: 'templates/deliveryman/view_order_close.html',
                controller: 'DeliverymanViewCloseCtrl'
            })
            .state('deliveryman.view_auxiliary',{
                url:'/view_auxiliary',
                templateUrl: 'templates/client/view_auxiliary.html',
                controller: 'DeliverymanViewAuxiliaryCtrl'
            })
            .state('deliveryman.checkout',{
                cache:false,
                url:'/checkout',
                templateUrl: 'templates/deliveryman/checkout.html',
                controller: 'DeliverymanCheckoutCtrl'
            })
            .state('deliveryman.summary',{
                url:'/summary',
                templateUrl:'templates/deliveryman/summary.html',
                controller: 'DeliverymanSummaryCtrl'
            });

        $urlRouterProvider.otherwise("/login");
        $provide.decorator('OAuthToken',['$localStorage','$delegate',function ($localStorage,$delegate) {
            Object.defineProperties($delegate,{
                setToken:{
                    value:function (data) {
                        return $localStorage.setObject('token',data);
                    },
                    enumarable:true,
                    configurable:true,
                    writable:true
                },
                getToken:{
                    value:function () {
                        return $localStorage.getObject('token');
                    },
                    enumarable:true,
                    configurable:true,
                    writable:true

                },
                removeToken:{
                    value:function () {
                        return $localStorage.setObject('token',null);
                    },
                    enumarable:true,
                    configurable:true,
                    writable:true

                }
            });
            return $delegate;
        }]);
    });

