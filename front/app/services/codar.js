'use strict';

app.service('codarService',['$http','$log','$translate','utilService',function($http, $log, $translate, utilService) {
		
				this.getCodares = function() {
					return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/codar');
				};				
				this.codarI18nFilter = function(input, field) {
					var language = $translate.use();
					return eval('input["'+language+'"].'+field);
				};

} ]);