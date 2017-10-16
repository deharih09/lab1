(function () {
    'use strict';

    angular
            .module('cpApp')
            .controller('HomeController', HomeController);

    HomeController.$inject = [ '$rootScope'];
    function HomeController(  $rootScope) {
        var hmc = this;     
        hmc.user = null;

        initController();

        function initController() {
            loadCurrentUser();

        }

        function loadCurrentUser() {
            hmc.user = $rootScope.globals.currentUser;
        }

    }
    })();
