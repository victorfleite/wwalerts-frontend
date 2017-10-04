'use strict';

app.controller('ModalAlertController', [ '$rootScope', '$scope','$log', '$filter', '$translate', 'close', 'emergenciaService', 'emergencia', function($rootScope, $scope, $log, $filter, $translate, close, emergenciaService, emergencia) {

	$scope.emergencia = angular.copy(emergencia);
	$scope.municipios = angular.copy(emergencia.municipios);
		
	var orderBy = $filter('orderBy');
	
	// Setar visibilidade do marker para false 
	//$scope.emergencia.configMarker.visible = false;
	
	// Variaveis Gerais da Aplicação 
	$rootScope.loading = false;
	// Enviar Evento solicitando ao MainController que esconda o header menu.
	$scope.$emit('setHeaderMenuVisibility', { headerMenuVisibility: false });
	// Enviar Evento solicitando ao MainController que esconda o Alerts menu.
	$scope.$emit('setAlertasMenuVisibility', { alertasMenuVisibility: false });
	
	$scope.emergenciaI18nFilter = angular.copy(emergenciaService.emergenciaI18nFilter);
			
	// Enviar Evento para MainController com Emergencia para ser plotada no mapa
	$scope.$emit('setEmergenciaOnMap', { emergencia: angular.copy($scope.emergencia) });
	
	$scope.orderarPor = function(predicate) {
	    $scope.predicate = predicate;
	    $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
	    $scope.municipios = orderBy($scope.municipios, predicate, $scope.reverse);
	};
	
	
	$scope.close = function (){
		// Enviar Evento solicitando ao MainController que remova a Emergencia do Mapa e volte a funcionar as emergencias por tabs.
		$scope.$emit('removeEmergenciaOnMap');
		// Enviar Evento solicitando ao MainController que mostre o header menu.
		$scope.$emit('setHeaderMenuVisibility', { headerMenuVisibility: true });
		// Enviar Evento solicitando ao MainController que mostre o Alerts menu.
		$scope.$emit('setAlertasMenuVisibility', { alertasMenuVisibility: true });
		close("Success!");
			
	}
	

} ]);