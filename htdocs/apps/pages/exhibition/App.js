/**
 * @author : Victor Aguilar
 *
 */
/* jshint browser: treue */
/* globals require, define, angular, domready */
(function(factory)
{
	'use strict';

	if( typeof define == 'function' && define.amd )
	{
		require([
			'angular',
			'domready',
			'angucomplete-alt',
			'pages.exhibition.directives/ExhibitionsDatepicker',
            'pages.exhibition.services/ExhibitionService',
            'ui.bootstrap',
			'angular-locale-mx',
            'ngRoute',
            'ngSanitize'
			],
			factory);
	}else{
		factory(angular,domready);
	}
})(function(angular, domready)
{
	'use strict';
	angular.module('App',[
		'pages.exhibition.directives.ExhibitionsDatepicker',
        'pages.exhibition.services.ExhibitionService',

        'ui.bootstrap',
		'ngLocale',
		'angucomplete-alt',
        'ngRoute',
        'ngSanitize'
		])

        .constant('CONFIG',{
            defaultFilter : {
                name : 'byMonth',
                title : 'Mes',
                data : new Date()
            },
            none: 'none'
        })

        .constant('routes', {
            'exhibition.show' : '/exhibition/:id/show',
            'exhibition.index'  : '/'
        })

        .config(['$routeProvider','routes', function($routeProvider, routes){

            $routeProvider
                .when(routes['exhibition.index'], {
                    templateUrl : '/apps/pages/exhibition/templates/index.html',
                    controller : 'pages.exhibition.list-controller'
                })
                .when(routes['exhibition.show'], {
                    templateUrl : '/apps/pages/exhibition/templates/show.html',
                    controller : 'pages.exhibition.show-controller'
                })
                .otherwise({
                    redirectTo : routes['exhibition.index']
                })
        }])

        .run(['datepickerConfig', 'moment',
            function(datepickerConfig, moment) {
                angular.extend(datepickerConfig, {
                    minDate : moment().subtract( moment().date() - 1, 'days').toDate(),
                    maxDate : moment( moment().year() + '-' + (moment().month() + 1) + '-' + moment()
                        .daysInMonth(),'YYYY-MM-DD').toDate(),
                    minMode : 'day',
                    maxMode : 'day',
                    showWeek : 'true'
                });
            }
        ])

        .controller('pages.exhibition.controllers.exhibitions-controller', [
            '$scope',
            '$location',
            'CONFIG',
            'pages.exhibition.services.ExhibitionService',
            'routes',
            function($scope, $location, CONFIG, Exhibition, routes){

                /*-------------------
                 * Bindings
                 -------------------*/

                $scope.filteredExhibitions  = [];
                $scope.selectedDay          = new Date();
                $scope.filter               = angular.copy(CONFIG.defaultFilter);
                $scope.advices              = Exhibition.titlesAndDirectories();
                $scope.filters              = Exhibition.filters();



                /*-------------------
                 * Methods
                 *------------------*/

                $scope.adviceSelected = function(selection){

                    if(angular.isUndefined(selection)) return;

                    var id = selection.originalObject.id;

                   	$location.url(routes['exhibition.show'].replace(':id', id));
                };



                /*------------------
                 * Events
                 *-----------------*/

                $scope.$on('dateSelected', function(event, data)
                {
                	if( data.name === 'byWeek')
                	{
                		var date = moment(data.value, moment.ISO_8601);

                		$scope.startDate = date.subtract(date.weekday(), 'days' ).valueOf();

                		$scope.endDate = date.add(6, 'days').valueOf();
                	}

                    $scope.selectedDay = data.value;

                    $scope.filter.name  = data.name;
                    $scope.filter.title = ''
                    $scope.filter.data  = data.value;
                });



                $scope.$on('$locationChangeSuccess', function(){

                    var filter = $location.search();

                    if( filter.name ){

                        $scope.filter.name  = filter.name
                        $scope.filter.title = filter.title
                        $scope.filter.data  = decodeURI( filter.data );
                    }else{

                        angular.extend($scope.filter, CONFIG.defaultFilter);
                    }

                    $scope.$root.$broadcast('filterUpdated', $scope.filter);
                });
        }])

        /**
         * Scope inherits filter object from its parent (exhibitions-controller)
         */
        .controller('pages.exhibition.list-controller', [
            '$scope',
            '$routeParams',
            'CONFIG',
            'pages.exhibition.services.ExhibitionService',
            function($scope, $rootParams, CONFIG, Exhibition){

                var filters      = Exhibition.filters();

                $scope.exhibitions  = Exhibition.all();

                $scope.exhibitionFilter = function(exhibition){

                    if($scope.filter.name == CONFIG.none){

                        return $scope.exhibitions;
                    }

                    return (filters[$scope.filter.name])(exhibition, $scope.filter.data);
                };
        }])

        .controller('pages.exhibition.show-controller', [
            '$scope',
            '$routeParams',
            '$location',
            '$log',
            '$sce',
            'pages.exhibition.services.ExhibitionService',
            function($scope, $routeParams, $location, $log, $sce, Exhibition) {

                var exhibitionData = Exhibition.find($routeParams.id);

                if(angular.isUndefined(exhibitionData)) {

                    $log.error('Any exhibition with id:', $routeParams.id);

                    $location.url('/');

                    return;
                }

                $scope.exhibition = Exhibition.make(exhibitionData);

                $scope.technicalCard = $scope.exhibition.getTechnicalCard();

                $scope.trailer = $sce.trustAsHtml($scope.exhibition.exhibition_film.film.trailer);

                $scope.isDefined = function(field){

                    // Anything that is not empty is accepted.
                    return field.value;
                };
        }])

        .filter("as", ['$parse', function($parse) {
            return function(collection, expresion) {
                return $parse(expresion).assign(this, collection);
            };
        }]);

	domready( function()
	{
        document
            .getElementsByTagName('body')[0]
            .setAttribute(
            'data-ng-controller',
            'pages.exhibition.controllers.exhibitions-controller'
        );
		angular.bootstrap(document, ['App']);
	});

});