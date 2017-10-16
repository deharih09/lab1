(function () {
    'use strict';

    angular
            .module('cpApp', ['ngRoute', 'ngCookies', 'ui.bootstrap', 'ui.bootstrap.tpls'])
            .config(config)
            .run(run);

    config.$inject = ['$routeProvider', '$locationProvider'];
    function config($routeProvider, $locationProvider) {
        $routeProvider
                .when('/', {
                    controller: 'HomeController',
                    templateUrl: 'home/home.view.html',
                    controllerAs: 'hmc'
                })

                .when('/gened', {
                    controller: 'GenedController',
                    templateUrl: 'gened/gened.view.html',
                    controllerAs: 'hmc'
                })

                .when('/login', {
                    controller: 'LoginController',
                    templateUrl: 'login/login.view.html',
                    controllerAs: 'vm'
                })
                .when('/prereq', {
                    controller: 'PrereqController',
                    templateUrl: 'prereq/prereq.view.html',
                    controllerAs: 'hmc'
                })
                .when('/requirement', {
                    controller: 'RequirementController',
                    templateUrl: 'requirement/requirement.view.html',
                    controllerAs: 'hmc'
                })

                .otherwise({redirectTo: '/login'});
    }

    run.$inject = ['$rootScope', '$location', '$cookieStore', '$http'];
    function run($rootScope, $location, $cookieStore, $http) {
        // keep user logged in after page refresh
        $rootScope.globals = $cookieStore.get('globals') || {};

        if ($rootScope.globals.currentUser) {
            $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata; // jshint ignore:line
        }

        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            // redirect to login page if not logged in and trying to access a restricted page
            var restrictedPage = $.inArray($location.path(), ['/login', '/prereq', '/requirement']) === -1;
            var loggedIn = $rootScope.globals.currentUser;

            if (restrictedPage && !loggedIn) {
                $location.path('/login');
            } else {
                if (typeof $rootScope.globals.currentUser != 'undefined')
                    $rootScope.userid = $rootScope.globals.currentUser.id;
            }
        });
    }

})();
