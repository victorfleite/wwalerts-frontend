'use strict';

app.service('emergenciaService', ['$http', '$log', '$translate', 'utilService', function ($http, $log, $translate, utilService) {

	this.getEmergenciasHoje = function () {
		return $http.get('data/today.json');
	};
	this.getEmergenciasAmanha = function () {
		return $http.get('data/tomorrow.json');
	}
	this.getEmergenciasFuturas = function () {
		return $http.get('data/future.json');
	}
	this.getEmergencia = function (id) {
		return $http.get('/index.php?r=frontend/front/emergencia&id=' + id);
	}
	this.emergenciaI18nFilter = function (input, field) {
		var language = $translate.use();
		return input[language][field];
	};


}]);