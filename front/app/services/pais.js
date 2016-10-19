'use strict';

app.service('paisService',['$http','$log','utilService',function($http, $log, utilService) {
	this.getMapCenterConfig = function() {		
		return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/mapcenterconfig');
	};
	this.getMapWmsConfig = function() {								
		return $http.get(utilService.getUrlServidor() + '/index.php?r=frontend/front/wmsconfig');
	};				

} ]);