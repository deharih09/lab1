(function () {
    'use strict';

    angular
        .module('cpApp')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$location', 'AuthenticationService', 'FlashService', '$rootScope'];
    function LoginController($location, AuthenticationService, FlashService, $rootScope) {
        var vm = this;

        vm.login = login;

        (function initController() {
            // reset login status
            AuthenticationService.ClearCredentials();
        })();

        function login() {
            vm.dataLoading = true;
            AuthenticationService.Login(vm.username, vm.password, function (response) {
                if (response.success) {
					var name = response.first + " " + response.last
					vm.user = {name: name, id : response.id};
					$rootScope.globals.currentUser = vm.user;
                    AuthenticationService.SetCredentials(vm.username, vm.password, response);
		    if (response.status == 1 )
			$location.path('/');
		    else 
                    	$location.path('/login');
                } else {
                       
                    	vm.dataLoading = false;
		    	$location.path('/login');
		    
                }
            });
        };
    }

})();
