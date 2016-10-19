'use strict';

app.service('emergenciaService',['$http','$log','$translate','utilService',function($http, $log, $translate, utilService) {
		
				this.getEmergenciasHoje = function() {
					return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/hoje');
				};
				this.getEmergenciasAmanha = function() {
					return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/amanha');
				}
				this.getEmergenciasFuturas = function() {   
					return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/futuro');
				}
				this.getEmergencia = function(id){
					return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/emergencia&id='+id);
				}
				this.emergenciaI18nFilter = function(input, field) {
					var language = $translate.use();
					return input[language][field];
				};
				

} ]);