angular.module('starter.services')
    .factory('ClientOrder',['$resource','appConfig',function ($resource,appConfig) {
        var url = appConfig.baseUrl + '/api/client/order/:id';
        return $resource(url,{id:'@id'},{
            query:{
                isArray: false
            }
        });
    }])
    .factory('DeliverymanOrder',['$resource','appConfig',function ($resource,appConfig) {
        var url = appConfig.baseUrl + '/api/deliveryman/order/:id';
        return $resource(url,{id:'@id'},{
            query:{
                isArray: false
            },
            updateStatus:{
                method: 'PATCH',
                url: url + '/update-status'
            },
            geo:{
                method: 'POST',
                url: url + '/geo'
            },
            count:{
                method: 'GET',
                url: appConfig.baseUrl +'/api/deliveryman/count'
            },
            countAnt:{
                method: 'GET',
                url: appConfig.baseUrl +'/api/deliveryman/countAnt'
            }
            ,countT:{
                method: 'GET',
                url: appConfig.baseUrl +'/api/deliveryman/countT'
            },
            countAntT:{
                method: 'GET',
                url: appConfig.baseUrl +'/api/deliveryman/countAntT'
            }
        });

    }]);