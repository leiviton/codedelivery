angular.module('starter.controllers')
    .controller('DeliverymanViewCloseCtrl',[
        '$scope','$state','$stateParams','DeliverymanOrder','$ionicLoading','$cordovaGeolocation','$ionicPopup','$cart','UserData',
        function ($scope,$state, $stateParams, DeliverymanOrder,$ionicLoading,$cordovaGeolocation,$ionicPopup,$cart,UserData) {
        var watch;
        $scope.user = UserData.get();
        $scope.order = [];
        var aux = $cart.getAux();
        if(aux.auxiliar.length == 0 || aux.auxiliar==null){
            aux.auxiliar = null;
            $scope.auxiliary = aux.auxiliar;
            console.log($scope.auxiliary);
        }else {
            $scope.auxiliary = aux.auxiliar;
            console.log($scope.auxiliary);
        }

            $scope.openListAuxiliares = function () {
                $state.go('deliveryman.checkout');
            };

        $ionicLoading.show({
           template: 'Carregando...'
        });
        DeliverymanOrder.get({id:$stateParams.id, include:"items,cupom"},function (data) {
            $scope.order = data.data;
            $ionicLoading.hide();
        },function (dataError) {
            $ionicLoading.hide();
        });

        $scope.goToDeliveryClose = function (o) {
            $ionicPopup.confirm({
                title: 'Atenção',
                template: 'Deseja fechar esta Ordem?'
            }).then(function(res) {
                $ionicLoading.show({
                    template: 'Enviando...'
                });
                if(res) {
                    var posOptions = {timeout: 30000, enableHighAccuracy: false, maximumAge: 0};

                    $cordovaGeolocation
                        .getCurrentPosition(posOptions)
                        .then(function (position) {
                            var lat = position.coords.latitude;
                            var long = position.coords.longitude;

                            console.log(lat,long);
                            var  ax = {auxiliary: angular.copy($scope.auxiliary)};
                            console.log(ax);
                            angular.forEach(ax.auxiliary,function (item) {
                                item.auxiliary_id = item.id;
                            });
                            console.log(ax);
                            DeliverymanOrder.updateStatus({id: $stateParams.id}, {
                                deliveryman_id: null,
                                status: 2,
                                lat: lat,
                                long: long,
                                service: o.service,
                                auxiliary: ax
                            },function (data) {
                                $scope.order = data;
                                console.log(data);
                                $ionicLoading.hide();
                                $state.go('deliveryman.order');
                            });
                        }, function(err) {
                            // error
                            $ionicLoading.hide();
                        });
                } else {
                    $ionicLoading.hide();
                }
            });

            /*DeliverymanOrder.updateStatus({id:$stateParams.id},{status:1},function () {
                var watchOptions = {
                    timeout:3000,
                    enableHighAccuracy:false
                };
                watch = $cordovaGeolocation.watchPosition(watchOptions);
                watch.then(null,function (responseError) {
                    //erros
                },function (position) {
                    DeliverymanOrder.geo({id:$stateParams.id},{
                        lat: position.coords.latitude,
                        long: position.coords.longitude
                    });
                });
            })*/
        };
        
        function stopWatchPosition() {
            if(watch && typeof watch =='object' && watch.hasOwnProperty('watchID')){
                $cordovaGeolocation.clearWatch(watch.watchID);
            }
        }
    }]);