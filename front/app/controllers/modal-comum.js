'use strict';

app.controller('ModalComumController', [ '$rootScope', '$scope','$log', '$translate', 'close', 'transfer', function($rootScope, $scope, $log, $translate, close, transfer) {
	// Variaveis Gerais da Aplicação 
	$rootScope.loading = false;
	
	$scope.transfer = transfer;
	// Enviar Evento solicitando ao MainController que esconda o header menu.
	$scope.$emit('setHeaderMenuVisibility', { headerMenuVisibility: false });
	// Enviar Evento solicitando ao MainController que esconda o Alerts menu.
	$scope.$emit('setAlertasMenuVisibility', { alertasMenuVisibility: false });
	
	
	$scope.close = function (){
		// Enviar Evento solicitando ao MainController que mostre o header menu.
		$scope.$emit('setHeaderMenuVisibility', { headerMenuVisibility: true });
		// Enviar Evento solicitando ao MainController que mostre o Alerts menu.
		$scope.$emit('setAlertasMenuVisibility', { alertasMenuVisibility: true });
		close("Success!");
	}
	
	

} ]);