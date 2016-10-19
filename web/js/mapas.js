/**
 * . User: derik Date: 19/06/12
 */

/* The Map */
var map;

/* Risk layer name */
var risk_layer;
var rain_layer;
var cities_layer;
var layer_vector;
var manual_risk_layer;
var metar_layer;
var surface_layer;
var goesir_layer;
var umidade_layer;
var temp_layer;
var temp_min_layer;
var temp_max_layer;
var prec_layer;
var rajada_layer;
var orvalho_layer;
var raio_layer;
var markers;
var layer_vector_futuro;
var markers_futuro;
var arrayUfs;

var goes_layer_name = "eumetsat_900913_png";

/* TMS server url */
// var RAIOS_SERVER = "http://www.simepar.br/cvestatico/tms/"
// var TMS_SERVER = "http://www.simepar.br/cvestatico/tms/"
// var JSON_SERVER = "http://www.simepar.br/cvestatico/tms/1.0.0/json/"
var RAIOS_SERVER = "http://alert-as.inmet.gov.br/tms/";
var TMS_SERVER = "http://alert-as.inmet.gov.br/tms/";
var JSON_SERVER = "http://alert-as.inmet.gov.br/tms/1.0.0/json";

var WMS_SERVER = "http://www.simepar.br/cvgeo/centrovirtualws/wms/";
var WMS_PAISES = "http://alert-as.inmet.gov.br:8080/geoserver/cvws/wms/";
var VISUALWEATHER_SERVER = "http://alert-as.inmet.gov.br/cv/proxy";
var VISUALWEATHER_SERVER_ALERT_AS = "http://alert-as.inmet.gov.br/cv/proxy_alert_as";
// var VISUALWEATHER_SERVER = "http://localhost:8090/cv/proxy";
// var VISUALWEATHER_SERVER_ALERT_AS =
// "http://localhost:8090/cv/proxy_alert_as";

var field_date;
var field_date_offset;
var field_date_eme;
var field_date_dur;
var dateOffset;
var risk_date;

var visual_weather_combo_data;
var visual_weather_opacity = 0.75;

var RISK = 0;
var OBSERVATION = 1;
var EMERGENCY = 2;
var SEVERE_EVENT = 3;
var ADDEMERGENCY = 4;
var PREVISAO = 4;
var VISUALWEATHER_METAR = 5;
var VISUALWEATHER_SURFACE = 6;
var VISUALWEATHER_GOESIR = 7;
var VISUALWEATHER_UMIDADE = 8;
var VISUALWEATHER_TEMP = 9;
var VISUALWEATHER_TEMP_MIN = 10;
var VISUALWEATHER_TEMP_MAX = 11;
var VISUALWEATHER_PREC = 12;
var VISUALWEATHER_RAJADA = 13;
var VISUALWEATHER_ORVALHO = 14;
var RAIO_INTERPOLADO = 15;

var mapType;

var idAction;
var MAX_ZOOM = 11;

/* Portuguese initialization for the jQuery UI date picker plugin. */
jQuery(function($) {
	$.datepicker.regional['pt'] = {
		closeText : 'Fechar',
		prevText : '<Anterior',
		nextText : 'Seguinte',
		currentText : 'Hoje',
		monthNames : [ 'Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio',
				'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro',
				'Dezembro' ],
		monthNamesShort : [ 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul',
				'Ago', 'Set', 'Out', 'Nov', 'Dez' ],
		dayNames : [ 'Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira',
				'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'S&aacute;bado' ],
		dayNamesShort : [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex',
				'S&aacute;b' ],
		dayNamesMin : [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'S&aacute;b' ],
		weekHeader : 'Sem',
		dateFormat : 'dd/mm/yy',
		firstDay : 0,
		isRTL : false,
		showMonthAfterYear : false,
		yearSuffix : ''
	};
	$.datepicker.setDefaults($.datepicker.regional['pt']);

	$.timepicker.regional['pt'] = {
		timeOnlyTitle : "Escolha o Horário",
		timeText : "Horário",
		hourText : "Hora",
		minuteText : "Minuto",
		secondText : "Segundo",
		currentText : "Agora",
		closeText : "Pronto"
	};
	$.timepicker.setDefaults($.timepicker.regional['pt']);
});

var datepicker_risk_options = {
	defaultDate : 0,
	// minDate: -10,
	maxDate : 0,
	onSelect : function(selectedDate, objDate) {
		dateOffset.setValue("1");
		refreshRiskLayer();
	}
};

var datepicker_emer_options = {
	defaultDate : 0,
	timeformat : 'hh:mm'
};

/**
 * Initializes risk page
 */
var initRisk = function(opt) {
	this.mapType = this.RISK;
	this.field_date = $(".date");
	this.field_date_offset = $(".date_offset");
	$(field_date_offset).change(function() {
		refreshRiskLayer();
	});
	loadDatePicker(this.field_date, this.datepicker_risk_options);
	createRiskMap("map", 3, opt.toolbar);
	return this.map;
};

/**
 * Initializes emergency page
 */
var initEmergency = function(opt) {
	this.field_date_eme = $(".date-eme");
	this.field_date_dur = $(".date-dur");
	loadTimePicker(this.field_date_eme, this.datepicker_emer_options);
	loadTimePicker(this.field_date_dur, this.datepicker_emer_options);
	var date = new Date();
	date.setDate(date.getDate() + 5);
	$(this.field_date_dur).datepicker('setDate', date);
	this.initRisk(opt);
};

/**
 * Initializes observation page
 */
var initObservation = function() {
	this.mapType = this.OBSERVATION;
	this.field_date = $(".date");
	this.createObservationMap("map")
}

var createMap = function(render_to) {
	var lon = -54;
	var lat = -26;
	var scale = 180 / 256;
	var scales = [ scale / Math.pow(2, 2), scale / Math.pow(2, 3),
			scale / Math.pow(2, 4), scale / Math.pow(2, 5),
			scale / Math.pow(2, 6), scale / Math.pow(2, 7),
			scale / Math.pow(2, 8), scale / Math.pow(2, 9),
			scale / Math.pow(2, 10), scale / Math.pow(2, 11),
			scale / Math.pow(2, 12) ];

	var navigation = new OpenLayers.Control.Navigation({
		handleRightClicks : false,
		zoomWheelEnabled : false,
		zoomBoxEnabled : false
	});

	var mapOptions = {
		resolutions : scales,
		controls : [
		/*
		 * new OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine(),
		 * new OpenLayers.Control.MousePosition(),
		 */
		/* new OpenLayers.Control.LayerSwitcher({'ascending': false}) */
		],
		projection : new OpenLayers.Projection("EPSG:900913"),
		displayProjection : new OpenLayers.Projection("EPSG:4326"),
		units : "m",
		maxExtent : new OpenLayers.Bounds(-20037508, -20037508, 20037508,
				20037508)
	}

	this.map = new OpenLayers.Map(render_to, mapOptions);

	var layer_osm = new OpenLayers.Layer.OSM();
	layer_osm.displayOutsideMaxExtent = false;
	layer_osm.displayInLayerSwitcher = false;
	this.map.addLayer(layer_osm);
	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
}

/*******************************************************************************
 * Create a new map
 * 
 * @param render_to
 *            The div where the map will be render
 * @param url
 *            Risk Cache map Url
 */
var createRiskMap = function(render_to, zoom, edit_toolbar) {
	this.createMap(render_to);
	var layername = getRiskLayerName();
	// console.log(layername);

	this.manual_risk_layer = new OpenLayers.Layer.WMS("Protocolos",
			this.WMS_SERVER, {
				layers : "emergencia",
				transparent : true
			}, {
				isBaseLayer : false
			});

	this.risk_layer = new OpenLayers.Layer.TMS("Riscos", this.TMS_SERVER, {
		layername : layername,
		type : 'png',
		maxExtent : new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
				-3211227.117022, 1794589.096147),
		isBaseLayer : false
	});
	this.risk_layer.displayInLayerSwitcher=false;
	this.map.addLayers([ this.risk_layer, this.manual_risk_layer ]);

	if (edit_toolbar) {
		var toolbar;
		var panel_controls;

		this.layer_vector = new OpenLayers.Layer.Vector("Vector");
		panelControls = [
				new OpenLayers.Control.Navigation(),
				new OpenLayers.Control.DrawFeature(this.layer_vector,
						OpenLayers.Handler.Polygon, {
							'displayClass' : 'olControlDrawFeaturePath'
						}) ];
		toolbar = new OpenLayers.Control.Panel({
			displayClass : 'olControlEditingToolbar',
			defaultControl : panelControls[0]
		});
		toolbar.addControls(panelControls);
		this.map.addLayer(this.layer_vector);
		this.map.addControl(toolbar);

		this.layer_vector.preFeatureInsert = function() {
			layer_vector.destroyFeatures(layer_vector.features);
		};

		this.layer_vector.onFeatureInsert = function() {
			var campo = $("#wkt");
			campo.val(layer_vector.features[0].geometry);
		};

	}

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
};

/**
 * Initialize the datepicker
 */
var loadDatePicker = function(element, options) {
	$.datepicker.setDefaults($.datepicker.regional["pt"]);
	$(element).datepicker(options);
	$(element).datepicker('setDate', new Date());
};

/**
 * Initialize the datepicker
 */
var loadTimePicker = function(element, options) {
	$.datepicker.setDefaults($.datepicker.regional["pt"]);
	$(element).datetimepicker(options);
	$(element).datetimepicker('setDate', new Date());
};

/**
 * Builds the layer name based on datepicker
 */
var getRiskLayerName = function() {
	var date = $(this.field_date).datepicker('getDate');
	var offset = new Number($(".date_offset").val());

	var arrDate = this.risk_date.split('/');
	var serverDate = new Date(arrDate[2], arrDate[1] - 1, arrDate[0])

	if (date > serverDate) {
		date.setDate(serverDate.getDate());
		$(this.field_date).datepicker('setDate', date);
		$('#aviso-mensagem-data').html(this.formatDate(date, true));
		$('#layer-info-yesterdayrisks-dialog').modal();
	}

	if (!isFinite(offset)) {
		var offset = new Number(dateOffset.getValue());
	}

	var from = this.formatDate(date, false);
	date.setDate(date.getDate() + offset);
	var to = this.formatDate(date, false);
	return "risco_" + from + "_" + to + "_900913_png"
};

var getRainLayerName = function() {
	var date = $(this.field_date).datepicker('getDate');
	var from = this.formatDate(date, false);
	// return "chuva_20120715_900913_png";
	return "chuva_" + from + "_900913_png";
};

/** Centers the map in the given lon, lat at a given zoom level */
var setCenter = function(opt) {
	this.map.setCenter(new OpenLayers.LonLat(opt.lon, opt.lat).transform(
			new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),
			opt.zoom);
};

var refresh = function() {
	switch (this.mapType) {
	case this.RISK:
		refreshRiskLayer();
		break;
	case this.OBSERVATION:
		refreshRainLayer();
		break;
	}
};

/**
 * Refreshs the risk layer, rendering the risk
 */
var refreshRiskLayer = function() {
	if ($(".date_offset").val() != "Nenhuma") {
		var layername = getRiskLayerName();
		this.map.removeLayer(this.risk_layer);
		this.map.removeLayer(this.cities_layer);
		this.risk_layer = new OpenLayers.Layer.TMS("Riscos", this.TMS_SERVER, {
			layername : layername,
			type : 'png',
			maxExtent : new OpenLayers.Bounds(-12184473.306305,
					-7555236.739448, -3211227.117022, 1794589.096147),
			isBaseLayer : false
		});
		this.risk_layer.displayInLayerSwitcher=false;
		this.risk_layer.redraw();
		this.map.addLayers([ this.risk_layer, this.cities_layer ]);
	} else {
		this.risk_layer.setVisibility(false);
	}
};

/**
 * Refreshs the risk layer, rendering the risk
 */
var refreshRainLayer = function() {
	var layername = getRainLayerName();
	// console.log(layername);
	this.map.removeLayer(this.rain_layer);
	this.map.removeLayer(this.cities_layer);
	this.rain_layer = new OpenLayers.Layer.TMS("Chuva", this.TMS_SERVER, {
		layername : layername,
		type : 'png',
		maxExtent : new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
				-3211227.117022, 1794589.096147),
		isBaseLayer : false
	});
	this.rain_layer.redraw();
	this.map.addLayers([ this.rain_layer, this.cities_layer ]);
};

/**
 * Returns a date in one of two formats: dd/mm/yyyy or yyyymmdd
 * 
 * @param date
 *            [Date] The date
 * @param human
 *            [Boolean] Human readable (format: dd/mm/yyyy) or risk directory
 *            label (format: yyyymmdd)
 */
var formatDate = function(date, human) {
	s = "";
	var m = date.getMonth() + 1;
	if (m < 10) {
		m = "0" + m
	}
	var d = date.getDate();
	if (d < 10) {
		d = "0" + d;
	}
	var y = date.getFullYear();
	if (human) {
		s = d + "/" + m + "/" + y;
	} else {
		s = y + "" + m + "" + d;
	}
	return s;
};

/**
 * Move date do the left
 * 
 * @param model
 *            [Boolean] True if model date, false otherwise
 */
var left = function(model) {
	var field;
	if (model) {
		field = this.field_date;
	} else {
		field = this.field_date_pred;
	}
	var date = $(field).datepicker('getDate');
	date.setDate(date.getDate() - 1);
	$(field).datepicker('setDate', date);
	$(field_date_offset).val(0)
	this.refresh();
};

/**
 * Move date do the right
 * 
 * @param model
 *            [Boolean] True if model date, false otherwise
 */
var right = function(model) {
	var field;
	if (model) {
		field = this.field_date;
	} else {
		field = this.field_date_pred;
	}
	var date = $(field).datepicker('getDate');
	date.setDate(date.getDate() + 1);
	$(field).datepicker('setDate', date);
	$(field_date_offset).val(0);
	this.refresh();
};

var showMap = function() {
	$("#map_field").show();
	resize_hero();
};

var cleanPolygon = function() {
	this.layer_vector.destroyFeatures(this.layer_vector.features);
};

var createMapObservacoes = function() {
	this.mapType = this.OBSERVATION;

	this.createMap("map");

	// this.goes_layer = new OpenLayers.Layer.TMS(
	// "Composição Eumetsat GOES <br /> <span class='goes-time right'> </span>",
	// this.TMS_SERVER, {
	// layername: goes_layer_name,
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true
	// }
	// );
	// this.map.addLayer(this.goes_layer);
	// getGoesDate();

	this.risco_precipitacao24_layer = new OpenLayers.Layer.TMS(
			"Precipitação - 24 horas", this.TMS_SERVER, {
				layername : "chuva24hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_precipitacao24_layer);

	this.risco_precipitacao12_layer = new OpenLayers.Layer.TMS(
			"Precipitação - 12 horas", this.TMS_SERVER, {
				layername : "chuva12hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_precipitacao12_layer);

	this.risco_precipitacao6_layer = new OpenLayers.Layer.TMS(
			"Precipitação - 6 horas", this.TMS_SERVER, {
				layername : "chuva6hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_precipitacao6_layer);

	this.risco_vento24_layer = new OpenLayers.Layer.TMS("Vento - 24 horas",
			this.TMS_SERVER, {
				layername : "vento24hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_vento24_layer);

	this.risco_vento12_layer = new OpenLayers.Layer.TMS("Vento - 12 horas",
			this.TMS_SERVER, {
				layername : "vento12hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_vento12_layer);

	this.risco_vento6_layer = new OpenLayers.Layer.TMS("Vento - 6 horas",
			this.TMS_SERVER, {
				layername : "vento6hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_vento6_layer);

	this.risco_temperatura24_layer = new OpenLayers.Layer.TMS(
			"Temperatura - 24 horas", this.TMS_SERVER, {
				layername : "temperatura24hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_temperatura24_layer);

	this.risco_temperatura12_layer = new OpenLayers.Layer.TMS(
			"Temperatura - 12 horas", this.TMS_SERVER, {
				layername : "temperatura12hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_temperatura12_layer);

	this.risco_temperatura6_layer = new OpenLayers.Layer.TMS(
			"Temperatura - 6 horas", this.TMS_SERVER, {
				layername : "temperatura6hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_temperatura6_layer);

	this.risco_umidade24_layer = new OpenLayers.Layer.TMS("Umidade - 24hrs",
			this.TMS_SERVER, {
				layername : "umidade24hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_umidade24_layer);

	this.risco_umidade12_layer = new OpenLayers.Layer.TMS("Umidade - 12hrs",
			this.TMS_SERVER, {
				layername : "umidade12hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_umidade12_layer);

	this.risco_umidade6_layer = new OpenLayers.Layer.TMS("Umidade - 6hrs",
			this.TMS_SERVER, {
				layername : "umidade6hrs_900913_png",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});
	this.map.addLayer(this.risco_umidade6_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.cities_layer.displayInLayerSwitcher = false;
	this.map.addLayer(this.cities_layer);

	// this.raios0015_layer = new OpenLayers.Layer.TMS(
	// "Raios - 15 min",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos0015minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios0015_layer);
	//
	// this.raios0030_layer = new OpenLayers.Layer.TMS(
	// "Raios - 30 min",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos0030minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios0030_layer);
	//
	// this.raios0060_layer = new OpenLayers.Layer.TMS(
	// "Raios - 60 min",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos0060minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios0060_layer);
	//
	// this.raios0180_layer = new OpenLayers.Layer.TMS(
	// "Raios - 3 horas",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos0180minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios0180_layer);
	//
	// this.raios0360_layer = new OpenLayers.Layer.TMS(
	// "Raios - 6 horas",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos0360minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios0360_layer);
	//
	// this.raios1440_layer = new OpenLayers.Layer.TMS(
	// "Raios - 24 horas",
	// this.RAIOS_SERVER, {
	// layername: "raiosimepar_ultimos1440minutos",
	// type: 'png',
	// maxExtent: new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
	// -3211227.117022, 1794589.096147),
	// isBaseLayer: false,
	// transparent: true,
	// visibility: false
	// }
	// );
	// this.map.addLayer(this.raios1440_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

var formatTimeWMS = function(datetime) {
	// var a = "2013-03-20T09:00:00Z";

	var year = datetime.substr(0, 4);
	var month = datetime.substr(5, 2);
	var day = datetime.substr(8, 2);
	var hour = datetime.substr(11, 5);
	// var minute = a.substr(14, 2);

	result = day + '/' + month + '/' + year + ' ' + hour;

	return result;
}

var getArrayTimeToCombo = function(array) {
	var result = new Array();
	for (var i = array.length - 1; i >= 0; i--) {
		var item = [ array[i], formatTimeWMS(array[i]) ];
		result.push(item);
	}

	return result;

}

// schema {obs, alert_as}
var getVisualWeatherCapabilities = function(schema, layer_name) {

	var times = new Array();

	jQuery.ajax({
		type : "GET",
		// url : "/cv/mapa/get_file?server=" + VISUALWEATHER_SERVER, //SIMEPAR
		// url : "/cv/mapa/get_file?server=http://192.168.99.45:8008/obs",
		// //INMET
		// url : "/cv/mapa/get_file?server=http://192.168.99.45:8008/alert_as",
		// //INMET
		url : "/cv/mapa/get_file?server=http://192.168.99.45:8008/" + schema, // INMET
		async : false,
		success : function(dataXML) {
			var xmlDoc = $.parseXML(dataXML);
			var xml = $(xmlDoc);
			// var title = xml.find("Dimension[name='time']");
			var title = xml.find("Capability>Layer>Layer[queryable='1']");

			$.each(title, function(index, value) {
				if ($(value).find("Name").text().indexOf(layer_name) !== -1) {
					var time = $(value).find("Dimension[name='time']").text();
					times = time.split(",");
				}

			});

			// console.log(title);
		},
		error : function() {
			// error handler..
		}
	});
	return times;
}

var createMapMetar = function() {
	this.mapType = this.VISUALWEATHER_METAR;

	this.createMap("map");

	metar_layer = new OpenLayers.Layer.WMS("Metar", VISUALWEATHER_SERVER, {
		layers : "METAR",
		transparent : true,
		isBaseLayer : false,
		crs : "EPSG:4326"
	}, {
		opacity : visual_weather_opacity,
		singleTile : true
	});

	this.map.addLayer(metar_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('obs', 'METAR');

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapSurface = function() {
	this.mapType = this.VISUALWEATHER_SURFACE;

	this.createMap("map");

	surface_layer = new OpenLayers.Layer.WMS("Surface", VISUALWEATHER_SERVER, {
		layers : "Surface",
		transparent : true,
		isBaseLayer : false,
		srs : "EPSG:4326"
	}, {
		opacity : visual_weather_opacity,
		singleTile : true
	});

	this.map.addLayer(surface_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);
	var times = getVisualWeatherCapabilities('obs', 'Surface');
	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

var createMapRaio = function() {

	this.mapType = this.RAIO_INTERPOLADO;

	this.createMap("map");

	raio_layer = new OpenLayers.Layer.WMS("raios_1h",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : "raios_1h",
				transparent : true,
				isBaseLayer : false,
				srs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(raio_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);
	var times = getVisualWeatherCapabilities('alert_as', 'raios_1h');
	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

var createMapGoesir = function() {
	this.mapType = this.VISUALWEATHER_GOESIR;

	this.createMap("map");

	goesir_layer = new OpenLayers.Layer.WMS("GOESIR",
			this.VISUALWEATHER_SERVER, {
				layers : "GOESIR",
				transparent : true,
				isBaseLayer : false,
				srs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(goesir_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	var times = getVisualWeatherCapabilities('obs', 'GOESIR');

	visual_weather_combo_data = getArrayTimeToCombo(times);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

var createMapUmidade = function() {

	this.mapType = this.VISUALWEATHER_UMIDADE;

	this.createMap("map");

	umidade_layer = new OpenLayers.Layer.WMS("Umidade",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : "automaticas_UR_horaria",
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(umidade_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as',
			'automaticas_UR_horaria');

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapTempMin = function() {

	var layer_name = "automaticas_tmin_horaria";

	this.mapType = this.VISUALWEATHER_TEMP_MIN;

	this.createMap("map");

	temp_min_layer = new OpenLayers.Layer.WMS("Temperatura Mínima",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(temp_min_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapTempMax = function() {

	var layer_name = "automaticas_tmax_horaria";

	this.mapType = this.VISUALWEATHER_TEMP_MAX;

	this.createMap("map");

	temp_max_layer = new OpenLayers.Layer.WMS("Temperatura Máxima",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(temp_max_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapTemp = function() {

	var layer_name = "automaticas_temp_horaria";

	this.mapType = this.VISUALWEATHER_TEMP;

	this.createMap("map");

	temp_layer = new OpenLayers.Layer.WMS("Temperatura",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(temp_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapPrec = function() {

	var layer_name = "automaticas_prec";

	this.mapType = this.VISUALWEATHER_PREC;

	this.createMap("map");

	prec_layer = new OpenLayers.Layer.WMS("Precipitação",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(prec_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapRajada = function() {

	var layer_name = "automaticas_rajada";

	this.mapType = this.VISUALWEATHER_RAJADA;

	this.createMap("map");

	rajada_layer = new OpenLayers.Layer.WMS("Rajada",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(rajada_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var createMapOrvalho = function() {

	var layer_name = "automaticas_temp_orvalho";

	this.mapType = this.VISUALWEATHER_ORVALHO;

	this.createMap("map");

	orvalho_layer = new OpenLayers.Layer.WMS("Orvalho",
			this.VISUALWEATHER_SERVER_ALERT_AS, {
				layers : layer_name,
				transparent : true,
				isBaseLayer : false,
				crs : "EPSG:4326"
			}, {
				opacity : visual_weather_opacity,
				singleTile : true
			});

	this.map.addLayer(orvalho_layer);

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true,
				visibility : false
			});

	this.map.addLayer(this.cities_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});

	var times = getVisualWeatherCapabilities('alert_as', layer_name);

	this.visual_weather_combo_data = getArrayTimeToCombo(times);

	return this.map
};

var update_date = function() {

	if (mapType == VISUALWEATHER_GOESIR) {
		goesir_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_SURFACE) {
		surface_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_METAR) {
		metar_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_UMIDADE) {
		umidade_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_TEMP) {
		temp_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_TEMP_MAX) {
		temp_max_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == VISUALWEATHER_TEMP_MIN) {
		temp_min_layer.mergeNewParams({
			'time' : this.value
		});
	} else if (mapType == RAIO_INTERPOLADO) {
		raio_layer.mergeNewParams({
			'time' : this.value
		});
	}

}

var createMapSatelite = function() {
	/*
	 * var lon = -54; var lat = -26; var scale = 180/256; var scales =
	 * [scale/Math.pow(2,2), scale/Math.pow(2,3), scale/Math.pow(2,4),
	 * scale/Math.pow(2,5), scale/Math.pow(2,6), scale/Math.pow(2,7),
	 * scale/Math.pow(2,8), scale/Math.pow(2,9)];
	 * 
	 * var mapOptions = { resolutions: scales, controls: [ new
	 * OpenLayers.Control.Zoom(), new OpenLayers.Control.ScaleLine(), new
	 * OpenLayers.Control.MousePosition(), new OpenLayers.Control.Navigation(), ],
	 * projection: new OpenLayers.Projection("EPSG:900913"), displayProjection:
	 * new OpenLayers.Projection("EPSG:4326"), units: "m", maxExtent: new
	 * OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508) }
	 * 
	 * this.map = new OpenLayers.Map(mapOptions);
	 * 
	 * var layer_osm = new OpenLayers.Layer.OSM("Base");
	 * layer_osm.displayOutsideMaxExtent = false; this.map.addLayer(layer_osm);
	 */
	this.createMap("map");

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.map.addLayer(this.cities_layer);

	this.goes_layer = new OpenLayers.Layer.TMS("Composição Eumetsat GOES",
			this.TMS_SERVER, {
				layername : goes_layer_name,
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.map.addLayer(this.goes_layer);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

var createMapModeloNumerico = function() {
	this.mapType = this.OBSERVATION;
	this.createMap("map")

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.cities_layer.displayInLayerSwitcher = false;

	this.map.addLayer(this.cities_layer);

	this.goes_layer = new OpenLayers.Layer.TMS(
			"Composição Eumetsat GOES <br /> <span id='goes' class='right'> </span>",
			this.TMS_SERVER, {
				layername : goes_layer_name,
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.map.addLayer(this.goes_layer);
	getGoesDate();

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
	return this.map
};

/*******************************************************************************
 * Create a new observation map
 * 
 * @param render_to
 *            The div where the map will be render
 * @param url
 *            Risk Cache map Url
 */
var createObservationMap = function(render_to, zoom) {
	this.createMap(render_to);
	this.loadDatePicker(this.field_date, {
		defaultDate : 0,
		minDate : -10,
		maxDate : 0,
		onSelect : function(selectedDate, objDate) {
			refreshRainLayer();
		}
	})

	var layername = getRainLayerName();
	// console.log(layername);

	this.rain_layer = new OpenLayers.Layer.TMS("Chuva", this.TMS_SERVER, {
		layername : layername,
		type : 'png',
		maxExtent : new OpenLayers.Bounds(-12184473.306305, -7555236.739448,
				-3211227.117022, 1794589.096147),
		isBaseLayer : false
	});
	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});

	this.map.addLayers([ this.rain_layer, this.cities_layer ]);

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
};

var createToolbar = function(opt) {
	var toolGroup = "toolGroup";

	var navAction = new GeoExt.Action({
		tooltip : "Pan",
		iconCls : "icon-pan",
		toggleGroup : toolGroup,
		group : toolGroup,
		allowDepress : true,
		deactivateOnDisable : true,
		map : map,
		control : new OpenLayers.Control.Navigation({
			handleRightClicks : false,
			zoomWheelEnabled : true,
			zoomBoxEnabled : false,
			enableZoomBox : function(event) {
				return;
			},
			defaultDblClick : function(event) {
				return;
			}
		})
	});

	var action_icon = "icon-city-info";
	if (opt && (opt.severe || opt.seleciona)) {
		action_icon = "icon-city-marker"
	}

	idAction = new GeoExt.Action({
		tooltip : "Identificador",
		iconCls : action_icon,
		toggleGroup : toolGroup,
		group : toolGroup,
		allowDepress : true,
		map : map,
		handler : function() {
			if (panelControls && panelControls[1] && panelControls[1].active) {
				panelControls[1].deactivate();
			}
		},
		control : new OpenLayers.Control.Click({
			handlerOptions : {
				"single" : true
			}
		})
	});
	console.log(this.map);
	var historyControl = new OpenLayers.Control.NavigationHistory();
	map.addControl(historyControl);

	var navPreviousAction = new GeoExt.Action({
		tooltip : "Zoom para vista anterior",
		iconCls : "icon-zoom-previous",
		disabled : false,
		control : historyControl.previous
	});

	var navNextAction = new GeoExt.Action({
		tooltip : "Zoom para próxima vista",
		iconCls : "icon-zoom-next",
		control : historyControl.next
	});

	var zoomin = new Ext.Button({
		handler : function() {
			if (this.map.getZoom() < this.MAX_ZOOM) {
				this.map.zoomIn();
			}
		},
		tooltip : "Zoom In",
		iconCls : "icon-zoom-in",
		scope : this
	});

	var zoomout = new Ext.Button({
		tooltip : "Zoom Out",
		handler : function() {
			this.map.zoomOut();
		},
		iconCls : "icon-zoom-out",
		scope : this
	});

	var zoomtotal = new Ext.Button({
		tooltip : "Zoom to Visible Extent",
		iconCls : "icon-zoom-visible",
		group : toolGroup,
		handler : function() {
			setCenter({
				lon : -52.76,
				lat : -19.65,
				zoom : 3
			})
		},
		scope : this
	});

	var layerinfo = new Ext.Button({
		tooltip : "Show information about layers",
		iconCls : "icon-info",
		group : toolGroup,
		handler : function() {
			addLayerInfoObserver();
			$("#layer-info-dialog").modal();
		},
		scope : this
	});

	var escala = new Ext.Button({
		tooltip : "Escala",
		iconCls : "icon-escala",
		group : toolGroup,
		handler : function() {
			$("#escala-dialog").modal();
		},
		scope : this
	});

	var layerToogle = new Ext.Button({
		tooltip : "Show information about layers",
		iconCls : "icon-border",
		group : toolGroup,
		handler : function() {
			cities_layer.setVisibility(!cities_layer.getVisibility());
		},
		scope : this
	});

	this.dateOffset = new Ext.form.ComboBox({
		fieldClass : "date_offset input-small insideToolbar",
		mode : 'local',
		editable : false,
		typeAhead : true,
		triggerAction : 'all',
		store : new Ext.data.SimpleStore({
			id : 0,
			fields : [ "offset", "name" ],
			data : [ [ "1", "24 horas" ], [ "2", "48 horas" ],
					[ "3", "72 horas" ], [ "4", "96 horas" ],
					[ "5", "120 horas" ], [ "0", "Nenhuma" ] ]
		}),
		valueField : 'offset',
		allowBlank : false,
		hiddenName : 'offset',
		displayField : 'name',
		forceSelection : true,
		listeners : {
			'select' : refresh
		}
	});
	dateOffset.setValue("1");

	var date = new Ext.form.TextField({
		fieldClass : "date input-small insideToolbar",
		margins : {
			top : "10px"
		},
		name : "dataModelo"
	});

	var help = new Ext.Button({
		tooltip : "Legenda",
		text : "Escala",
		handler : function() {
			$("scale-modal").modal("toogle");
		},
		scope : this
	});

	var combo = new Ext.form.ComboBox({
		fieldClass : "input-medium insideToolbar",
		typeAhead : true,
		triggerAction : 'all',
		lazyRender : false,
		mode : 'local',
		store : new Ext.data.ArrayStore({
			id : 0,
			fields : [ 'myId', 'displayText' ],
			data : visual_weather_combo_data
		}),
		valueField : 'myId',
		displayField : 'displayText',
		listeners : {
			'select' : update_date
		}
	});

	var tools = [ navAction, zoomin, zoomout, zoomtotal, navPreviousAction,
			navNextAction, idAction, layerToogle ];

	if (opt && opt.riscos) {
		tools = tools.concat([ new Ext.Toolbar.Separator(),
				new Ext.Toolbar.TextItem({
					text : "Análise: "
				}), date, new Ext.Toolbar.TextItem({
					text : "&nbsp;&nbsp;Previsão: "
				}), dateOffset, new Ext.Toolbar.Separator() ]);
	} else if (this.mapType == this.OBSERVATION) {
		tools = tools.concat([ layerinfo, escala ]);
	} else if ((this.mapType == this.VISUALWEATHER_METAR)
			|| (this.mapType == this.VISUALWEATHER_GOESIR)
			|| (this.mapType == this.VISUALWEATHER_SURFACE)
			|| (this.mapType == this.VISUALWEATHER_UMIDADE)
			|| (this.mapType == this.VISUALWEATHER_TEMP)
			|| (this.mapType == this.VISUALWEATHER_TEMP_MAX)
			|| (this.mapType == this.VISUALWEATHER_TEMP_MIN)) {
		tools = tools.concat([ new Ext.Toolbar.Separator(),
				new Ext.Toolbar.TextItem({
					text : "Tempo: "
				}), combo, new Ext.Toolbar.Separator() ]);
	}

	var field_city = new Ext.form.TextField({
		fieldClass : "input-small insideToolbar",
		margins : {
			top : "10px"
		},
		name : "field_cidade"
	});

	var search_city = new Ext.Button(
			{
				tooltip : "Ok",
				text : "Ok",
				handler : function() {

					if (field_city.getValue() != "") {
						$
								.getJSON(
										'/cv/mapa/cidade_by_nome?cidade='
												+ field_city.getValue()
												+ '&callback=?',
										function(data) {
											if (data
													&& data.resultado.length != 0) {
												if (data.resultado.length == 1) {
													zoomByArea(data.resultado[0].geom);
												} else {
													$('#select_cidades_modal')
															.empty();
													$
															.each(
																	data.resultado,
																	function(
																			key,
																			value) {
																		$(
																				'#select_cidades_modal')
																				.append(
																						$(
																								"<option></option>")
																								.attr(
																										"value",
																										value.geom)
																								.text(
																										value.city));
																	});

													$("#choose_city_modal")
															.modal();
												}
											}
										});
					}
				},
				scope : this
			});
	
	var ufs_atingidas = new Ext.Button(
			{
				tooltip : "Clique para verificar as UFs atingidas pelo polígono.",
				text : "Check UFs",
				xtype: 'button',
				//cls : 'mybutton',
				//style: "color: #fff; background-color: #0074cc;background-image: -ms-linear-gradient(top, #0088cc, #0055cc);background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0055cc));background-image: -webkit-linear-gradient(top, #0088cc, #0055cc);",
				handler : function() {
					var start = new Date().getTime();

					$("#imgEspera").css("display", "block");
					$('#ufsTxt').text("Processando...");
					var layersInsert= map.getLayersByName("Vector Criando");
					var layerInsert=layersInsert[0];
					var ufWkt;
					var ufpoly;
					var ufsAtingidas;
					ufsAtingidas="";
					var contadorUfs = 0;
					
					if(layerInsert.features.length > 0){
						//setTimeout( function (){
							
							var the_geom = layerInsert.features[0].geometry;				
													
							
							var request = OpenLayers.Request.POST({
							    url: "/cv/emergencia/ufsAtingidasPeloPoligono",
							    data: OpenLayers.Util.getParameterString({country_id: 32, polygon: the_geom}),
							    headers: {
							        "Content-Type": "application/x-www-form-urlencoded"
							    },
							    callback: function (request) {
							    	var str = request.responseText+'';
									// do something with the response 
									$('#ufsTxt').html('<b>'+str+'</b> ('+str.split(',').length+')');
									$("#imgEspera").css("display", "none");
									var end = new Date().getTime();
									var time = end - start;
									alert('UFs atingidas: '+str+' ('+str.split(',').length+').\n O processo levou: ' + (time/1000) + ' segundos.');
								
								}
							})
									
							
						//},500);
					}
					else{
						alert("Não existem polígonos desenhados!");
						$('#ufsTxt').text("Não existem polígonos desenhados!");
						$("#imgEspera").css("display", "none");
					}
				},
				scope : this
			});
	
	tools = tools.concat([ new Ext.Toolbar.TextItem({
		text : "Cidade: "}), field_city, search_city ]);
	if (opt && opt.riscos) {
		tools = tools.concat([ new Ext.Toolbar.Separator(), ufs_atingidas, new Ext.Toolbar.Separator(),new Ext.Toolbar.TextItem({text : "", id:"ufsTxt"}), '<img id="imgEspera" src="../images/gif-load.gif" style="display:none">']);
	}
	return tools;

};

var zoomByArea = function(geom) {

	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(geom);
	var bounds = features.geometry.getBounds();

	var center = bounds.getCenterLonLat();

	var lonlat = new OpenLayers.LonLat(center.lon, center.lat).transform(map
			.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

	$.getJSON('/cv/mapa/cidade?lon=' + lonlat.lon + '&lat=' + lonlat.lat
			+ '&callback=?', function(data) {
		fillCity(data.resultado);
	});
	setCenter({
		lon : lonlat.lon,
		lat : lonlat.lat,
		zoom : 8
	});
	// this.map.zoomToExtent(bounds, false);
}

var createMapRisco = function() {
	this.createMap('map');
	return this.map
};

/**
 * Initializes risk page
 */
var initSimpleRisk = function(opt) {
	this.mapType = this.RISK;
	var mapPanel;
	var map = createMapRisco();

	var store = new GeoExt.data.LayerStore({
		map : map,
		layers : map.layers
	});
	tree = new Ext.tree.TreePanel({
		title : "Camadas",
		renderTo : "treepanel",
		root : new GeoExt.tree.LayerContainer({
			text : 'Riscos',
			leaf : false,
			expanded : true,
			layerStore : store
		}),
		enableDD : true,
	});

	mapPanel = new GeoExt.MapPanel({
		title : "Mapa de riscos",
		renderTo : "map",
		stateId : "map",
		map : map,
		tbar : createToolbar(opt)
	});
	this.field_date = $(".date");
	this.field_date_offset = $(".date_offset");
	$(field_date_offset).change(function() {
		refreshRiskLayer();
	});
	loadDatePicker(this.field_date, this.datepicker_risk_options);
	addRiskLayer(3, opt);
};

var initSimpleEmergency = function(opt) {
	
	var mapPanel;
	this.mapType = this.EMERGENCY;
	this.map = createMapRisco();

	var store = new GeoExt.data.LayerStore({
		map : map,
		layers : map.layers
	});

	if ((opt && !opt.severe) && (opt && opt.riscos)) {
		if (opt.camadas)
			var tree = new Ext.tree.TreePanel({
				title : "Camadas",
				renderTo : "treepanel",
				root : new GeoExt.tree.LayerContainer({
					text : 'Riscos',
					leaf : false,
					expanded : true,
					layerStore : store
				}),
				enableDD : true
			});
	}

	/*mapPanel = new GeoExt.MapPanel({
		// title: "Mapa de protocolo operacional",
		renderTo : "map",
		stateId : "map",
		map : map,
		tbar : createToolbar(opt)
	});*/

	if (opt && opt.toolbar) {
		//alert('alow');
		this.map.id="mapa_alertas";
		//console.log(this.map);
		this.mapType = this.RISK;
		this.field_date = $(".date");
		this.field_date_offset = $(".date_offset");
		$(field_date_offset).change(function() {
			refreshRiskLayer();
		});
		loadDatePicker(this.field_date, this.datepicker_risk_options);
		this.field_date_eme = $(".date-eme");
		this.field_date_dur = $(".date-dur");
		loadTimePicker(this.field_date_eme, this.datepicker_emer_options);
		loadTimePicker(this.field_date_dur, this.datepicker_emer_options);
		var date = new Date();
		// date.setDate(date.getDate() + 5);
		date.setDate(date.getDate());
		$(this.field_date_dur).datepicker('setDate', date);
	}
	;
	this.addRiskLayer(3, opt);

	if (opt && opt.observer) {
		console.log(this.map);
		this.layer_vector = new OpenLayers.Layer.Vector({
			layers : "Poligonos"
		}, {
			displayInLayerSwitcher : false
		});
		this.map.addLayer(this.layer_vector);
		try {
			this.drawPolygon($("#wkt").attr("value"), $("#wkt").data("color"));
		} catch (e) {
			console.log("No wkt found " + e);
		}
	}
	;

	if (opt && opt.cap) {
		this.layer_vector = new OpenLayers.Layer.Vector({
			layers : "Poligonos"
		}, {
			displayInLayerSwitcher : false
		});
		this.map.addLayer(this.layer_vector);

		try {
			// var p = 'POLYGON ((-5959940.2065481 -3988401.7990212,
			// -5930588.3876906 -4007969.6782595, -5827857.0216897
			// -3895454.3726394, -5788721.2632131 -3753587.2481618,
			// -5685989.8972121 -3694883.610447, -5578366.5614016
			// -3548124.5161599, -5553906.7123537 -3489420.878445,
			// -5509878.9840676 -3416041.3313015, -5416931.5576858
			// -3318201.9351101, -5387579.7388283 -3186118.7502517,
			// -5372903.8293996 -3156766.9313942, -5407147.6180666
			// -3000223.897488, -5358227.9199709 -2907276.4711062,
			// -5382687.7690188 -2873032.6824392, -5460959.2859719
			// -2980656.0182497, -5549014.7425442 -3318201.9351101,
			// -5607718.380259 -3342661.7841579, -5627286.2594973
			// -3469852.9992067, -5637070.1991164 -3538340.5765407,
			// -5793613.2330227 -3738911.3387331, -5857208.8405471
			// -3704667.5500661, -5959940.2065481 -3944374.0707351,
			// -5959940.2065481 -3988401.7990212))';

			this.drawPolygon2($('#wkt').attr('value'), $('#wkt').data('color'));
			// this.drawPolygon2(p, 'red');
		} catch (e) {
			console.log("No wkt found " + e);
		}
	}
	;

	if (opt && opt.country) {
		/*
		 * this.layer_vector = new OpenLayers.Layer.Vector({ layers:
		 * "Poligonos"}, {displayInLayerSwitcher: false});
		 * this.map.addLayer(this.layer_vector); try {
		 * this.drawPolygonCountry($('#wkt').attr('value'),
		 * $('#wkt').data('color')); //this.drawPolygon2(p, 'red'); } catch(e) {
		 * console.log("No wkt found " + e); }
		 */
		this.wms_paises_layers = new OpenLayers.Layer.WMS("Protocolos",
				this.WMS_PAISES, {
					layers : "cvws:" + opt.pais,
					transparent : true,
				}, {
					isBaseLayer : false,
					visibility : true,
					displayInLayerSwitcher : false
				});
		this.map.addLayer(this.wms_paises_layers);
		this.layer_vector = new OpenLayers.Layer.Vector("Avisos Hoje",{
			layers : "Avisos Hoje"
		}, {
			displayInLayerSwitcher : false
		});
		this.map.addLayer(this.layer_vector);

		this.layer_vector_futuro = new OpenLayers.Layer.Vector("Avisos Futuros",{
			layers : "Avisos Futuros"
		}, {
			displayInLayerSwitcher : false
		});
		this.map.addLayer(this.layer_vector_futuro);

		this.markers = new OpenLayers.Layer.Markers("Markers Hoje");
		this.map.addLayer(this.markers);

		this.markers_futuro = new OpenLayers.Layer.Markers("Markers Futuro");
		this.map.addLayer(this.markers_futuro);

		if (opt.mapaCentral){
			var lSwitcher=new OpenLayers.Control.LayerSwitcher({'position':"right-corner",'ascending':true});
			this.map.addControl(lSwitcher);
			lSwitcher.maximizeControl();
			lSwitcher.dataLbl.innerText = "Avisos:"
			
			layer_vector.displayInLayerSwitcher=true;
			layer_vector_futuro.displayInLayerSwitcher=true;
			this.layer_vector_futuro.setVisibility(false);
			markers.displayInLayerSwitcher=false;
			markers_futuro.displayInLayerSwitcher=false;
			
			layer_vector.trigger=1;
			layer_vector_futuro.trigger=1;
			markers.mapaPortal=1;
			markers_futuro.mapaPortal=1;
			
			this.map.events.register("changelayer", map, function (e) {
				if (e.layer.name == "Avisos Futuros" || e.layer.name == "Avisos Hoje"){
					if (e.layer.trigger == 1) {
						if (e.layer.name == "Avisos Futuros"){
							layer_vector.trigger=0;
							layer_vector_futuro.trigger=0;
							layer_vector.setVisibility(false);
							layer_vector_futuro.setVisibility(true);
							markers.setVisibility(false);
							markers_futuro.setVisibility(true);
						}
						if (e.layer.name == "Avisos Hoje"){
							layer_vector_futuro.trigger=0;
							layer_vector.setVisibility(true);
							layer_vector_futuro.setVisibility(false);
							markers.setVisibility(true);
							markers_futuro.setVisibility(false);
						}
						layer_vector.trigger=1;
						layer_vector_futuro.trigger=1;
					}
				}
			});
		}
		
		this.cities_layer.setVisibility(false);

		this.setCenter({
			lon : -55.00,
			lat : -15.00,
			zoom : 4
		});
	};
	
	if (opt && opt.southamerica) {
		this.layer_vector = new OpenLayers.Layer.Vector({
			layers : "Poligonos"
		}, {
			displayInLayerSwitcher : false
		});
		this.map.addLayer(this.layer_vector);
	}
	;

	if (opt && opt.severe) {
		this.layer_vector = new OpenLayers.Layer.Vector("Poligonos");
		this.layer_vector.displayInLayerSwitcher = false;
		this.map.addLayer(this.layer_vector);
		this.mapType = this.SEVERE_EVENT;
		this.field_date = $(".date");
		loadDatePicker(this.field_date, this.datepicker_emer_options);
	}
};

var initSimpleEmergencyExternal = function(opt) {
	var mapPanel;
	this.createMap('map');

	var store = new GeoExt.data.LayerStore({
		map : this.map,
		layers : map.layers
	});

	this.layer_vector = new OpenLayers.Layer.Vector({
		layers : "Poligonos"
	}, {
		displayInLayerSwitcher : false
	});
	this.map.addLayer(this.layer_vector);
	this.addObserver();
	try {
		var protocolos = $("[name='protocolo']");

		for (i = 0; i < protocolos.size(); i++) {
			this.drawPolygon($(protocolos[i]).attr('wkt'), $(protocolos[i])
					.attr('color'));
		}
	} catch (e) {
		console.log("No wkt found " + e);
	}

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});

	this.cities_layer.displayInLayerSwitcher = false;

	this.map.addLayer(this.cities_layer);

	map.events.register('click', map, getProtocolData);

	mapPanel = new GeoExt.MapPanel({
		// title: "Mapa de protocolo operacional",
		renderTo : "map",
		stateId : "map",
		map : this.map,
		tbar : createToolbar(opt)
	});

};

var getProtocolData = function(e) {
	var lonlat = map.getLonLatFromPixel(e.xy);
	lonlat = new OpenLayers.LonLat(lonlat.lon, lonlat.lat).transform(map
			.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

	$.getJSON('/cv/emergencia/show_risk_data?lon=' + lonlat.lon + '&lat='
			+ lonlat.lat + '&callback=?', function(data) {
		if (data && data.resultado != "") {
			$("#example").popover('destroy');
			$("#example").popover({
				trigger : 'manual',
				html : true,
				title : '',
				content : data.resultado
			});
			$(".popover-text").css("left", e.clientX + "px");
			$(".popover-text").css("top", e.clientY + "px");
			$("#example").popover('show');
		} else {
			$("#example").popover('hide');
		}
	});

};

var addRiskLayer = function(zoom, opt) {

	this.manual_risk_layer = new OpenLayers.Layer.WMS("Protocolos",
			this.WMS_SERVER, {
				layers : "emergencia",
				transparent : true,
			}, {
				isBaseLayer : false,
				visibility : false,
				displayInLayerSwitcher : false
			});

	this.map.addLayer(this.manual_risk_layer);

	if (this.mapType == this.RISK) {
		var layername = getRiskLayerName();
		this.risk_layer = new OpenLayers.Layer.TMS("Riscos", this.TMS_SERVER, {
			layername : layername,
			type : 'png',
			maxExtent : new OpenLayers.Bounds(-12184473.306305,
					-7555236.739448, -3211227.117022, 1794589.096147),
			isBaseLayer : false
		});
		this.risk_layer.displayInLayerSwitcher=false;
		this.map.addLayer(this.risk_layer);
	}

	this.cities_layer = new OpenLayers.Layer.TMS("Municípios", this.TMS_SERVER,
			{
				layername : "contorno",
				type : 'png',
				maxExtent : new OpenLayers.Bounds(-12184473.306305,
						-7555236.739448, -3211227.117022, 1794589.096147),
				isBaseLayer : false,
				transparent : true
			});
	this.cities_layer.displayInLayerSwitcher = false;

	this.map.addLayer(this.cities_layer);
	
	var vertexStyle = {
		    strokeColor: "#FF0000",
		    fillColor: "#FF0000",
		    strokeOpacity: 1,
		    strokeWidth: 2,
		    pointRadius: 6,
		    graphicName: "circle"
	};

	var virtual = {
	    strokeColor: "#EE9900",
	    fillColor: "#EE9900",
	    strokeOpacity: 1,
	    strokeWidth: 2,
	    pointRadius: 4,
	    graphicName: "circle"
	};
	
	var styleMap = new OpenLayers.StyleMap({
	    "default": OpenLayers.Feature.Vector.style['default'],
	    "vertex": vertexStyle
	}, {extendDefault: false});

	if (opt && opt.toolbar) {
		
		var toolbar;
		var panel_controls;
		var controle_vector = true;

		this.layer_vector = new OpenLayers.Layer.Vector("Vector");
		this.layer_vector.displayInLayerSwitcher=false;
		//console.log("affff");
		avisos_hoje = new OpenLayers.Layer.Vector("Hoje",{layers : "Hoje"}, {displayInLayerSwitcher : false});
		this.map.addLayer(avisos_hoje);
		avisos_futuros = new OpenLayers.Layer.Vector("Futuros",{layers : "Futuros"}, {displayInLayerSwitcher : false});
		this.map.addLayer(avisos_futuros);
		var ano=new Date();
		ano=ano.getFullYear();
		var mes=new Date();
		mes=mes.getMonth();
		var dia=new Date();
		dia=dia.getDate();
		var fim_dia_hoje=new Date(ano,mes,dia,23,59,59);
		fim_dia_hoje=fim_dia_hoje.format('Y-m-d H:i:s');
		var temAviso=0;
		$('.avisos_correntes').each(function(i, obj) {
		    /*console.log($(obj).data('wkt'));
		    /console.log($(obj).data('cor'));
			console.log($(obj).data('inicio'));
			console.log($(obj).data('fim'));*/
		    drawPolygon_avisos($(obj).data('wkt'), $(obj).data('cor'), $(obj).data('inicio'), $(obj).data('fim'), fim_dia_hoje);
		    $(obj).remove();
		    temAviso++;
		});
		avisos_hoje.setVisibility(false);
		avisos_futuros.setVisibility(false);
		if (temAviso > 0){
			var lSwitcher=new OpenLayers.Control.LayerSwitcher({'position':"right-corner",'ascending':true});
			this.map.addControl(lSwitcher);
			lSwitcher.maximizeControl();
			lSwitcher.dataLbl.innerText = "Avisos Correntes"
		}
		var layer_vector_polygon = new OpenLayers.Layer.Vector("Vector Criando",{styleMap: styleMap});
		layer_vector_polygon.displayInLayerSwitcher=false;
		panelControls = [
				new OpenLayers.Control.Navigation({title:'Navegar'}),
				// new OpenLayers.Control.DrawFeature(this.layer_vector,
				// OpenLayers.Handler.Polygon, {'displayClass':
				// 'olControlDrawFeaturePath'}),
				//editaPoligono
				new OpenLayers.Control.ModifyFeature(layer_vector_polygon, {'displayClass' : 'olControlDrawFeaturePoint', vertexRenderIntent: "vertex", virtualStyle: virtual, title:'Editar Polígono'}),
				new OpenLayers.Control.DrawFeature(layer_vector_polygon,
						OpenLayers.Handler.Polygon, {
							'displayClass' : 'olControlDrawFeaturePath', title:'Criar Polígono'
						}) ];
		toolbar = new OpenLayers.Control.Panel({
			displayClass : 'olControlEditingToolbar',
			defaultControl : panelControls[0]
		});
		toolbar.addControls(panelControls);
		this.map.addLayer(this.layer_vector);
		this.map.addLayer(layer_vector_polygon);
		this.map.addControl(toolbar);

		var panelNav = new OpenLayers.Control.PanZoom();
		this.map.addControl(panelNav);

		panelControls[1].featureAdded = function() {
			// layer_vector_polygon.destroyFeatures(layer_vector_polygon.features);
		}
		
		function featureModified() {
			var campo = $("#wkt");
			$('#ufsTxt').html('');
			
			campo.val(layer_vector_polygon.features[0].geometry);
		}
		
		layer_vector_polygon.events.on({
			featuremodified: featureModified
		});

		layer_vector_polygon.preFeatureInsert = function() {
			idAction.control.deactivate();
			// layer_vector.destroyFeatures(layer_vector.features);
			$("#table-geos").find('tbody').empty();
		};

		layer_vector_polygon.onFeatureInsert = function() {
			// controle_vector = false;
			// $.getJSON('/cv/mapa/cidades_by_area?geo=' +
			// layer_vector_polygon.features[0].geometry + '&callback=?',
			// function(data) {
			// // $.each(data.resultado, function(index, element) {

			// // $("#table-geos").find('tbody').append($('<tr value='
			// +element.id+'>').append($('<td>').html("<input type='hidden'
			// value='"+element.geo+"' name='municipios' /> ")));
			// // drawPolygon(element.geo, '#FF9400');
			// // });
			// drawPolygon(data.resultado, '#FF9400');
			var campo = $("#wkt");
			campo.val(layer_vector_polygon.features[0].geometry);
			// });

		};

		layer_vector_polygon.preFeatureInsert = function() {
			// idAction.control.deactivate();
			layer_vector_polygon.destroyFeatures(layer_vector_polygon.features);
			// $("#table-geos").find('tbody').empty();
		};
		   
	};

	this.map.zoomToMaxExtent();
	this.setCenter({
		lon : -66.76,
		lat : -35.65,
		zoom : 3
	});
};

var addObserver = function() {
	var cbox = $(".cbox");
	$(cbox).change(function() {
		if ($(this).is(":checked")) {
			drawPolygon($(this).data("wkt"), $(this).data("color"));
		} else {
			removePolygon($(this).data("wkt"));
		}
	})
};

var addLayerInfoObserver = function() {
	var obsUrl = JSON_SERVER + "/obs.json?callback=?";
	var goesUrl = JSON_SERVER + "/goes.json?callback=?";
	var raiosUrl = RAIOS_SERVER + "1.0.0/json/raios.json?callback=?";
	$.getJSON(obsUrl);
	$.getJSON(goesUrl);
	$.getJSON(raiosUrl);
};

var getGoesDate = function() {
	var goesUrl = JSON_SERVER + "/goes.json?callback=?";
	$.getJSON(goesUrl);
};

var obsCallback = function(data) {
	$.each(data, function(index, obj) {
		$(obj.id).html(obj.to);
	});
};
var goesCallback = function(obj) {
	$(obj.id).html(obj.time);
};
var raiosCallback = function(obj) {
	$(obj.id).html(obj.time);
};

var fillCity = function(element) {
	var features;

	var wkt = new OpenLayers.Format.WKT();

	if (element.geo)
		features = wkt.read(element.geo);
	else
		features = wkt.read(element.toString());

	// layer_vector_polygon.destroyFeatures(layer_vector_polygon.features);
	if (this.mapType == ADDEMERGENCY)
		layer_vector.destroyFeatures(layer_vector.features);

	var bounds;
	var existent = false;
	if (features) {
		for (var i = 0; i < layer_vector.features.length; i++) {
			if (features.geometry.equals(layer_vector.features[i].geometry)) {
				layer_vector.removeFeatures(layer_vector.features[i]);
				existent = true;
				break;
			}
		}
	}
	if (!existent) {
		if (element.geo)
			this.drawPolygon(element.geo);
		else
			this.drawPolygon(element.toString());
		this.addtoTable(element);
	} else {
		this.removeFromTable(element);
	}

	if (mapType == ADDEMERGENCY) {
		var campo = $("#wkt");
		if (element.geo) {
			campo.val(element.geo);
		} else {
			campo.val(element.toString());
		}
	}
};

var addtoTable = function(element) {
	$("#table-city").find('tbody').append(
			$('<tr value=' + element.id + '>').append(
					$('<td>').html(
							"<input type='checkbox' checked value='"
									+ element.id
									+ "' name='municipios' class='hide' /> "
									+ element.cidade)));
	resize_hero();
};

var removeFromTable = function(element) {
	$.each($("#table-city tbody tr"), function(index, row) {
		if ($(row).attr("value") == element.id) {
			$(row).remove();
		}
		;
	});
};

var managePolygon = function(element) {
	if ($(element).is(":checked")) {
		if (!isChecked(element)) {
			drawPolygon($(element).data("wkt"), $(element).data("color"));
		}
	} else {
		removePolygon($(element).data("wkt"));
	}
};

var formatCapPolygon = function(polygon) {
	var newPolygon = '';
	var arrTokens = polygon.split(' ');
	for (var i = 0; i < arrTokens.length; i++) {

		var cordenadas = arrTokens[i].split(',');
		newPolygon += cordenadas[1] + ' ' + cordenadas[0] + ',';
	}
	newPolygon = newPolygon.substring(0, newPolygon.length - 1);
	return 'POLYGON ((' + newPolygon + '))';
}

var drawCapPolygon = function(element, color) {

	drawPolygon(element.data("wkt"), element.attr("color"));

	/*
	 * var newPolygon = formatCapPolygon(polygon); console.log(newPolygon); var
	 * wkt = new OpenLayers.Format.WKT(); var features = wkt.read(newPolygon);
	 * if(features) { if(color) { features.style = { fillOpacity: 0.4,
	 * strokeColor: color, fillColor: color } //features.style = { fillOpacity:
	 * 0.4, strokeColor: '0000FF', fillColor: '0000FF' } }
	 * layer_vector.addFeatures(features); } else { console.log('Bad WKT'); }
	 */

};

var isChecked = function(element) {
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read($(element).data("wkt"));
	if (features) {
		for (var i = 0; i < layer_vector.features.length; i++) {
			if (features.geometry.equals(layer_vector.features[i].geometry)) {
				return true
			}
		}
	}
	return false;
};

var drawPolygon_avisos = function(element, color, inicio, fim, fim_dia_hoje) {
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(element);
	if (features) {
		if (color) {
			var features_futuro = wkt.read(element);
			features_futuro.style = {
				fillOpacity : 1,
				strokeColor : color,
				fillColor : color
			}
			features.style = {
				fillOpacity : 1,
				strokeColor : color,
				fillColor : color
			}
		}
		//console.log("anoia");
		if ((inicio <= fim_dia_hoje) && (fim <= fim_dia_hoje || fim > fim_dia_hoje)){
			avisos_hoje.addFeatures(features);
		}
		if (inicio > fim_dia_hoje || fim >= fim_dia_hoje){
			avisos_futuros.addFeatures(features_futuro);
		}
	} else {
		console.log('Bad WKT');
	}
};

var drawPolygon = function(element, color) {
	//console.log(element);
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(element);
	if (features) {
		if (color) {
			features.style = {
				fillOpacity : 0.7,
				strokeColor : color,
				fillColor : color
			}
			// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
			// fillColor: '0000FF' }
		}
		layer_vector.addFeatures(features);
	} else {
		console.log('Bad WKT');
	}
};

var drawPolygon2 = function(polygon, color) {

	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(polygon);
	var bounds = features.geometry.getBounds();
	var center = bounds.getCenterLonLat();
	var lonlat = new OpenLayers.LonLat(center.lon, center.lat);
	setCenter({
		lon : lonlat.lon,
		lat : lonlat.lat,
		zoom : 5
	});

	console.log(polygon);

	features.geometry.transform(map.displayProjection, map
			.getProjectionObject());
	cities_layer.setVisibility(false);

	if (features) {
		if (color) {
			features.style = {
				fillOpacity : 0.7,
				strokeColor : color,
				fillColor : color
			}
			// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
			// fillColor: '0000FF' }
		}
		layer_vector.addFeatures(features);

	} else {
		console.log('Bad WKT');
	}
};

function createPopup(location, evento)
{
	var lonlat = new OpenLayers.LonLat(location.lon, location.lat);
	var newPopup= new OpenLayers.Popup.Anchored("Popup", lonlat, null, '<div align="center"><b>'+evento.toUpperCase()+'</b><br>Clique no marcador para mais informações.</div>', null, false);
	return newPopup;
}

function clickMarcador(cap)
{
	window.location.assign(cap)
}

function clickMarcadorPortal(cap)
{
	window.open(cap, '_blank');
}

var drawPolygonCountry = function(polygon, color, semZoom, typeCss, marcador, evento, cap) {
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(polygon);
	var bounds = features.geometry.getBounds();
	var center = bounds.getCenterLonLat();
	var lonlat = new OpenLayers.LonLat(center.lon, center.lat);

	if (!semZoom) {
		setCenter({
			lon : lonlat.lon,
			lat : lonlat.lat,
			zoom : 4
		});
	}

	features.geometry.transform(map.displayProjection, map
			.getProjectionObject());
	cities_layer.setVisibility(false);

	if (features) {
		if (typeCss == 'hoje') {
			if (color) {
				features.style = {
					fillOpacity : 0.7,
					strokeColor : color,
					fillColor : color
				}
				// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
				// fillColor: '0000FF' }
			}
			features.name = "hoje"
			layer_vector.addFeatures(features);

			features.attributes = {
				typeCss : typeCss
			};
			layer_vector.redraw();

			var location = new OpenLayers.LonLat(lonlat.lon, lonlat.lat)
					.transform(new OpenLayers.Projection("EPSG:4326"), map
							.getProjectionObject());			
			var size = new OpenLayers.Size(36, 54);
			var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);
			var icon = new OpenLayers.Icon(
					marcador,
					size, offset);
			if (markers.mapaPortal == 1){
				var marker = new OpenLayers.Marker(location, icon.clone());
				marker.name = "hoje";
				marker.location=location;
				marker.attributes = {
					typeCss : typeCss,
					mapaPortal: 1
				};
			}
			else{
				var marker = new OpenLayers.Marker(location, icon.clone());
				marker.name = "hoje";
				marker.location=location;
				marker.attributes = {
					typeCss : typeCss
				};
			}
			markers.addMarker(marker);

		}
		else if (typeCss == 'futuro')
		{
			if (color) {
				features.style = {
					fillOpacity : 0.7,
					strokeColor : color,
					fillColor : color
				}
				// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
				// fillColor: '0000FF' }
			}
			features.name = "futuro";
			features.attributes = {
				typeCss : typeCss
			};
			layer_vector_futuro.addFeatures(features);

			layer_vector_futuro.redraw();

			var location = new OpenLayers.LonLat(lonlat.lon, lonlat.lat)
					.transform(new OpenLayers.Projection("EPSG:4326"), map
							.getProjectionObject());
			
			var size = new OpenLayers.Size(36, 54);
			var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);
			var icon = new OpenLayers.Icon(
					marcador,
					size, offset);
			if (markers_futuro.mapaPortal == 1){
				var marker = new OpenLayers.Marker(location, icon.clone());
				marker.name = "futuro";
				marker.location=location;
				marker.attributes = {
					typeCss : typeCss,
					mapaPortal: 1
				};
			}
			else{
				var marker = new OpenLayers.Marker(location, icon.clone());
				marker.name = "futuro";
				marker.location=location;
				marker.attributes = {
					typeCss : typeCss
				};
			}
			markers_futuro.addMarker(marker);
		}
		//EVENTO: Mouse Click nos marcadores
		marker.events.register('click', marker, function(evt) {
			if (this.attributes.mapaPortal == 1){
		    	clickMarcadorPortal(cap);
		    }
		    else{
		    	clickMarcador(cap);
		    }
		});
		//EVENTO: Mouse Over nos marcadores
		marker.events.register('mouseover', marker, function(evt) {
			
			var el = evt.srcElement;
			$(el).attr('title', evento + '<br>Clique para mais informações.');
			$(el).tooltip('show');
			$(el).css('cursor','pointer');
		});
		//EVENTO: Mouse Out nos marcadores
		marker.events.register('mouseout', marker, function(evt) {
			
			//console.log(evt);
			//popup.hide();
		});
		
	} else {
		console.log('Bad WKT');
	}
};

var drawPolygonCountrySouthAmerica = function(polygon, color, semZoom) {

	// console.log(polygon);

	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(polygon);
	/*
	 * var bounds = features.geometry.getBounds(); var center =
	 * bounds.getCenterLonLat(); var lonlat = new OpenLayers.LonLat(center.lon,
	 * center.lat);
	 * 
	 * if(!semZoom){ setCenter({ lon: lonlat.lon, lat: lonlat.lat, zoom: 4}); }
	 * 
	 */
	features.geometry.transform(map.displayProjection, map
			.getProjectionObject());
	cities_layer.setVisibility(false);

	if (features) {
		if (color) {
			features.style = {
				fillOpacity : 0.7,
				strokeColor : color,
				fillColor : color
			}
			// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
			// fillColor: '0000FF' }
		}
		layer_vector.addFeatures(features);

	} else {
		console.log('Bad WKT');
	}
};

var removePolygon = function(element) {
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read(element);
	var bounds;
	if (features) {
		for (var i = 0; i < layer_vector.features.length; i++) {
			if (features.geometry.equals(layer_vector.features[i].geometry)) {
				layer_vector.removeFeatures(layer_vector.features[i]);
				break;
			}
		}
	} else {
		console.log('Bad WKT');
	}
};
var loading = false;

var getCity = function(e) {
	var lonlat = map.getLonLatFromViewPortPx(e.xy);
	lonlat = new OpenLayers.LonLat(lonlat.lon, lonlat.lat).transform(map
			.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
	$.getJSON('/cv/mapa/cidade?lon=' + lonlat.lon + '&lat=' + lonlat.lat
			+ '&callback=?', function(data) {
		if (mapType == SEVERE_EVENT || mapType == ADDEMERGENCY) {
			// console.log(data.resultado);
			fillCity(data.resultado);
			$(".spinner").addClass("hideIt");
			loading = !loading;

			// $("#table-city").html(data.resultado.cidade)
		} else if (data.resultado.riscos != ''
				&& (mapType == RISK || mapType == EMERGENCY)) {
			var riscos = data.resultado.riscos.split("|");
			$("#risco-dia-1").html(riscos[0].split(':')[0]);
			$("#risco-dia-2").html(riscos[1].split(':')[0]);
			$("#risco-dia-3").html(riscos[2].split(':')[0]);
			$("#risco-dia-4").html(riscos[3].split(':')[0]);
			$("#risco-dia-5").html(riscos[4].split(':')[0]);

			$("#risco-nivel-1")
					.addClass(getRiscoClass(riscos[0].split(':')[1]));
			$("#risco-nivel-2")
					.addClass(getRiscoClass(riscos[1].split(':')[1]));
			$("#risco-nivel-3")
					.addClass(getRiscoClass(riscos[2].split(':')[1]));
			$("#risco-nivel-4")
					.addClass(getRiscoClass(riscos[3].split(':')[1]));
			$("#risco-nivel-5")
					.addClass(getRiscoClass(riscos[4].split(':')[1]));

			$("#nome-cidade").html(data.resultado.cidade);
			$("#layer-info-city-dialog").modal();
		} else {
			$("#nome-cidadeonly").html(data.resultado.cidade);
			$("#layer-info-onlycity-dialog").modal();
		}
	});
}

OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
	defaultHandlerOptions : {
		'single' : true,
		'double' : false,
		'pixelTolerance' : 0,
		'stopSingle' : false,
		'stopDouble' : false
	},
	initialize : function(options) {
		this.handlerOptions = OpenLayers.Util.extend({},
				this.defaultHandlerOptions);
		OpenLayers.Control.prototype.initialize.apply(this, arguments);
		this.handler = new OpenLayers.Handler.Click(this, {
			'click' : this.onClick
		}, this.handlerOptions);
	},
	onClick : function(evt) {
		if (!loading) {
			console.log("not loading");
			$(".spinner").removeClass("hideIt");
			getCity(evt);
			loading = !loading;
		}
	}
});

var getRiscoClass = function(risco) {
	if (risco == '1') {
		return 'risco1';
	} else if (risco == '2') {
		return 'risco2';
	} else if (risco == '3') {
		return 'risco3';
	} else if (risco == '4') {
		return 'risco4';
	}
}

var verifyRiskLayer = function(mapType) {
	this.mapType = mapType;
	$.getJSON(JSON_SERVER + '/riscos.json?callback=?');
}

var riscosCallback = function(data) {
	risk_date = data.time.split(' ')[0];
	if (this.mapType == this.RISK) {
		initSimpleRisk({
			riscos : true
		});
		setCenter({
			lon : -52.76,
			lat : -19.65,
			zoom : 3
		})
	} else if (this.mapType == this.EMERGENCY) {
		initSimpleEmergency({
			toolbar : false,
			observer : true
		});
		setCenter({
			lon : -58,
			lat : -27,
			zoom : 3
		});
	} else if (this.mapType == this.ADDEMERGENCY) {
		initSimpleEmergency({
			toolbar : true,
			riscos : true,
			camadas : false,
			seleciona : true
		});
		setCenter({
			lon : -58.19,
			lat : -26,
			zoom : 3
		});
		this.mapType = ADDEMERGENCY;
	} else if (this.mapType == this.PREVISAO) {
		$(data.id).html(data.time);
	}
}

function getURLParameter(name) {
	return decodeURIComponent((location.search.match(RegExp("[?|&]" + name
			+ '=(.+?)(&|$)')) || [ , null ])[1]);
}
