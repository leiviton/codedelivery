angular.module('starter.controllers')
    .controller('DeliverymanOrderCtrl',[
        '$scope','$state','$ionicLoading','$stateParams','$ionicActionSheet','DeliverymanOrder','$ionicPopup','$cordovaGeolocation',
        function ($scope, $state,$ionicLoading,$stateParams,$ionicActionSheet,DeliverymanOrder,$ionicPopup,$cordovaGeolocation) {

        $scope.items = [];

        $ionicLoading.show({
           template: 'Carregando...'
        });



        $scope.doRefresh = function () {
          getOrders().then(function (data) {

              $scope.items = data.data;
              console.log(data.data);
              $scope.$broadcast('scroll.refreshComplete');
          },function (dataError) {
              $scope.$broadcast('scroll.refreshComplete');
          });
        };

            $scope.giveBack = function (o) {

                $ionicPopup.confirm({
                    title: 'Atenção',
                    template: 'Deseja devolver esta Ordem?'
                }).then(function(res) {
                    if(res) {
                        var posOptions = {timeout: 30000, enableHighAccuracy: false, maximumAge: 0};

                        $cordovaGeolocation
                            .getCurrentPosition(posOptions)
                            .then(function (position) {
                                var lat = position.coords.latitude;
                                var long = position.coords.longitude;

                                console.log(lat,long);

                                DeliverymanOrder.updateStatus({id: o.id}, {
                                    devolver:1,
                                    status: 0,
                                    lat: lat,
                                    long: long
                                },function (data) {
                                    console.log(data);
                                    $ionicLoading.hide();

                                });
                            }, function(err) {
                                // error
                                $ionicLoading.hide();
                            });
                    } else {
                        $ionicLoading.hide();
                    }
                });
            };
        $scope.openOrderDetail = function (order) {
            console.log(order);
            if (order.status == 1) {
                $state.go('deliveryman.view_close', {id: order.id});
            }else {
                $state.go('deliveryman.view_order', {id: order.id});
            }
        };
            $scope.showActionSheet = function (order) {
                $ionicActionSheet.show({
                    buttons:[
                        {text:'Ver detalhes'}
                        ],
                    titleText:'O que fazer?',
                    cancelText:'Cancelar',
                    cancel:function () {

                    },
                    buttonClicked:function (index) {
                        switch (index){
                            case 0:
                                if (order.status == 1) {
                                    $state.go('deliveryman.view_close', {id: order.id});
                                }else {
                                    $state.go('deliveryman.view_order', {id: order.id});
                                }
                                break;
                            case 1:
                                break
                        }
                    }
                });
            };
            function getOrders() {
                return DeliverymanOrder.query({
                    id:null,
                    orderBy:'created_at',
                    sortedBy:'asc'
                }).$promise;
            }

            getOrders().then(function (data) {
                if(data.data.length==0){
                    $ionicPopup.alert({
                        title: 'Atenção',
                        template: 'Não existe novas Ordens'
                    }).then(function(res) {
                        if(res){
                            $state.go('deliveryman.home');
                        }else{
                            $state.go('deliveryman.home');
                        }
                    });
                }
                $scope.items = data.data;
                $ionicLoading.hide();
            },function (dataError) {
                $ionicLoading.hide();
            });
    }]);