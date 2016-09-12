angular.module('starter.controllers')
    .controller('DeliverymanSummaryCtrl',[
        '$scope','DeliverymanOrder',
        function ($scope,DeliverymanOrder) {

            DeliverymanOrder.count({id:null,status:2},function (data) {
                $scope.atual = data[0];
            });
            DeliverymanOrder.countAnt({id:null,status:2},function (data) {
                $scope.anterior = data[0];
                if ($scope.atual < $scope.anterior) {
                    console.log($scope.atual, $scope.anterior);
                    $scope.menor = true;
                } else {
                    $scope.menor = false;
                }
            });
            DeliverymanOrder.count({id:null,status:0},function (data) {
                $scope.atualP = data[0];
            });
            DeliverymanOrder.countAnt({id:null,status:0},function (data) {
                $scope.anteriorP = data[0];
                if ($scope.atualP > $scope.anteriorP) {

                    $scope.menorP = true;
                } else {
                    $scope.menorP = false;
                }
            });
            DeliverymanOrder.countT({id:null},function (data) {
                $scope.atualT = data[0];
            });
            DeliverymanOrder.countAntT({id:null},function (data) {
                $scope.anteriorT = data[0];

                if ($scope.atualT < $scope.anteriorT){
                    console.log($scope.atualT, $scope.anteriorT);
                    $scope.menorT = true;
                } else {
                    $scope.menorT = false;
                }
            });


    }]);