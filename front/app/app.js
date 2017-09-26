'use strict';

/**
 * @ngdoc overview
 * @name alertAsApp
 * @description
 * # alertAsApp
 *
 * Main module of the application.
 */
var app = angular.module('alertAsApp', [  
    'ngRoute',                                   
	'openlayers-directive',
	'pascalprecht.translate', 
	'angularModalService',
	'FBFullScreen',
	'720kb.socialshare'
])
        /* define 'config2' constant - which is available in Ng's config phase */
		.constant('LOADER', { 
            LOADED_CLASS: 'pace-done',
            LOADING_CLASS: 'pace-progress' 
         })
        .constant('CONSTANTES', { 
            VIEW_FOLDER: 'app-minify',
            //URL_SERVIDOR: 'http://{{HOST}}/alert2-as/web' 
            URL_SERVIDOR: 'http://alertas/web'  
         })
        .config(['$logProvider', 'CONSTANTES', '$translateProvider', '$routeProvider', function($logProvider, CONSTANTES, $translateProvider, $routeProvider) {
        		
        	  $translateProvider.translations('pt-BR', {
        	    'TAB.HOJE'				: 'Hoje',
        	    'TAB.AMANHA'			: 'Amanhã',
        	    'TAB.FUTURO'			: 'Próximos Dias',
        	    'MSG_SEM_EVENTO'		: 'Sem Evento',
        	    'HEADERMENU.ABOUT'		: 'Sobre o Alert-AS',
        	    'HEADERMENU.TERMS' 		: 'Termos e Condições',
        	    'HEADERMENU.CONTACT'	: 'Contato',
        	    'HEADERMENU.HELP'		: 'Ajuda',
        	    'HEADERMENU.FULLSCREEN'	: 'Tela Cheia',
              'HEADERMENU.FORECAST' : 'Previsão do Tempo',
        	    'RISK.LEGENDA'			: 'LEGENDA',
        	    'RISK.LABEL_LEGENDA'	: 'Nível de Severidade',
        	    'RISK.NOTHING'			: 'Nada Reportado',
        	    'RISK.POTENTIAL'		: 'Perigo Potencial',
        	    'RISK.DANGER'			: 'Perigo',
        	    'RISK.DANGEROUS'		: 'Muito Perigoso',
        	    'ALERTMODAL_DATAINICIO' : 'Início',
        	    'ALERTMODAL_DATAFIM' 	: 'Fim',
        	    'ALERTMODAL_RISCOEMPOTENCIAL' : 'Risco em Potencial',
        	    'ALERTMODAL_RECOMENDACOES': 'Recomendações',
        	    'ALERTMODAL_AREASAFETADAS': 'Áreas Afetadas',
        	    'ALERTMODAL_NENHUM_RESULTADO':'Nenhum resultado'
        	  });        	  
        	  
        	  $translateProvider.translations('en', {
          	    'TAB.HOJE'	: 'Today',
          	    'TAB.AMANHA': 'Tomorrow',
          	    'TAB.FUTURO': 'Next Days',
          	    'MSG_SEM_EVENTO'		: 'Nothing Reported',
          	    'HEADERMENU.ABOUT'		: 'About Alert-As',
	      	    'HEADERMENU.TERMS' 		: 'Terms and Conditions',
	      	    'HEADERMENU.CONTACT'	: 'Contact',
	      	    'HEADERMENU.HELP'		: 'Help',
	      	    'HEADERMENU.FULLSCREEN'	: 'Full Screen',
              'HEADERMENU.FORECAST' : 'Weather Forecast',
	      	    'RISK.LEGENDA'			: 'CAPTION',
	      	    'RISK.LABEL_LEGENDA'	: 'Severity Levels',
        	    'RISK.NOTHING'			: 'Nothing Reported',
        	    'RISK.POTENTIAL'		: 'Potential',
        	    'RISK.DANGER'			: 'Danger',
        	    'RISK.DANGEROUS'		: 'Very Dangerous',
        	    'ALERTMODAL_DATAINICIO' : 'Start',
        	    'ALERTMODAL_DATAFIM' 	: 'End',
        	    'ALERTMODAL_RISCOEMPOTENCIAL' : 'Event Description',
        	    'ALERTMODAL_RECOMENDACOES': 'Warning',
        	    'ALERTMODAL_AREASAFETADAS': 'Affected Areas',
        	    'ALERTMODAL_NENHUM_RESULTADO':'Nothing Founded'
          	  });
        	  
        	  $translateProvider.translations('es', {
          	    'TAB.HOJE'				: 'Hoy',
          	    'TAB.AMANHA'			: 'Mañana',
          	    'TAB.FUTURO'			: 'Próximos días',
          	    'MSG_SEM_EVENTO'		: 'Sin Evento',
          	    'HEADERMENU.ABOUT'		: 'Acerca del Alert-AS',
          	    'HEADERMENU.TERMS' 		: 'Términos y Condiciones',
          	    'HEADERMENU.CONTACT'	: 'Contacto',
          	    'HEADERMENU.HELP'		: 'Ayuda',
          	    'HEADERMENU.FULLSCREEN'	: 'Pantalla Completa',
                'HEADERMENU.FORECAST' : 'Previsión del Tiempo',
          	    'RISK.LEGENDA'			: 'LEYENDA',
          	    'RISK.LABEL_LEGENDA'	: 'Gravedad',
          	    'RISK.NOTHING'			: 'Nada previsto',
          	    'RISK.POTENTIAL'		: 'Peligro Potencial',
          	    'RISK.DANGER'			: 'Peligro',
          	    'RISK.DANGEROUS'		: 'Gran Peligro',
          	    'ALERTMODAL_DATAINICIO' : 'Principio',
            	'ALERTMODAL_DATAFIM' 	: 'Fin',
        	    'ALERTMODAL_RISCOEMPOTENCIAL' : 'Descripción del evento',
        	    'ALERTMODAL_RECOMENDACOES': 'Recomendaciones',
        	    'ALERTMODAL_AREASAFETADAS': 'Las Áreas Afectadas',
        	    'ALERTMODAL_NENHUM_RESULTADO':'No encontrado',
          	  }); 
        	  
        	  //$translateProvider.useSanitizeValueStrategy('sanitize');
        	  $translateProvider.preferredLanguage('en');
        		
        	  $routeProvider.when('/history/:id', {
                controller: 'mainController',
                template: " "	
              });
        	  
        	  
        	
        }]).// Inicializa Variaveis de Sistema
        run(['$location', '$log','$rootScope', 'CONSTANTES', function($location, $log, $rootScope,  CONSTANTES) { // instance-injector
        
        	
        }]);
