(function () {
    'use strict';

    angular
        .module('cpApp')
        .factory('UserService', UserService);

    UserService.$inject = ['$http'];
    function UserService($http) {
        var service = {};

        service.CheckUser = CheckUser;
        service.CheckDuplicate = CheckDuplicate;
        service.Create = Create;
        service.Reset = Reset;
        service.Update = Update;
        service.UpdatePwd = UpdatePwd;
        service.Delete = Delete;

        return service;

        function Reset(user) {
            return $http.post('login/resetpassword', user).then(handleSuccess, handleError('Error changing password'));
        }

        function UpdatePwd(user) {
            return $http.post('login/updatepassword', user).then(handleSuccess, handleError('Error changing password'));
        }

        function CheckUser(user) {
            return $http.post('login/checkuser', user).then(handleSuccess, handleError('Error getting user by id'));
        }

        function CheckOldPassword(user) {
            return $http.post('login/checkduplicate', user).then(handleSuccess, handleError('Error creating user'));
        }

        function CheckDuplicate(user) {
            return $http.post('login/checkduplicate', user).then(handleSuccess, handleError('Error creating user'));
        }

        function Create(user) {
            return $http.post('login/adduser', user).then(handleSuccess, handleError('Error creating user'));
        }

        function Update(user) {
            return $http.put('/api/users/' + user.id, user).then(handleSuccess, handleError('Error updating user'));
        }

        function Delete(id) {
            return $http.delete('/api/users/' + id).then(handleSuccess, handleError('Error deleting user'));
        }

        // private functions

        function handleSuccess(res) {
            return res.data;
        }

        function handleError(error) {
            return function () {
                return { success: false, message: error };
            };
        }
    }

})();
