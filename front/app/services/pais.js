'use strict';

app.service('paisService',['$http','$log','utilService',function($http, $log, utilService) {
	this.getMapCenterConfig = function() {		
		return $http.get('data/map-config.json');
	};
	this.getMapWmsConfig = function() {								
		return $http.get('data/wms.json');
	};				

} ]);