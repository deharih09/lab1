(function () {
    'use strict';

    angular
            .module('cpApp')
            .controller('PrereqController', PrereqController);

    PrereqController.$inject = ['UserService', 'CourseService', '$rootScope', '$http'];
    function PrereqController(UserService, CourseService, $rootScope, $http) {
        var hmc = this;

        hmc.subjects = [{name: 'Art', description: "Art"}];
        hmc.subject = {};
        hmc.courseList = [];
        hmc.course_info = {};
        hmc.user = null;
        hmc.index = 0;


        hmc.info = {id: -1};
        hmc.state = {id: -1};


        hmc.save = save;

        hmc.getPrereqList = getPrereqList;
        hmc.displayPreviousItem = displayPreviousItem;
        hmc.displayNextItem = displayNextItem;


        initController();

        function initController() {
            loadCurrentUser();
            getSubjectList();
        }

        function loadCurrentUser() {
            hmc.user = $rootScope.globals.currentUser;
        }

        function getSubjectList() {

            $http({
                method: 'GET',
                url: "http://localhost/uwcfrm/course/subjectList"
            }).success(function (data, status, headers, config) {
                hmc.subjects = data;
                hmc.subject = hmc.subjects[0];
            });
        }
        function  getPrereqList() {
            var action = "http://localhost/uwcfrm/course/prereqList/" + hmc.subject;
            $http({
                method: 'GET',
                url: action
            }).success(function (data, status, headers, config) {
                if (data.length > 0) {
                    hmc.courseList = data;
                    hmc.course_info = angular.copy(hmc.courseList[0]);
                    hmc.index = 0;
                }

            });
        }
        function getCategoryList(category) {
            var action = 'http://localhost/uwcfrm/degree/getCategoryList/';
            $http.get(action + category).then(function (res) {
                hmc[category] = res.data;
                hmc.info = res.data[0];

            });
        }

        function save() {
            var item = hmc.course_info;

            var req_data = {id: item.id, infixPrereq: item.infixPrereq};

            $http.post('http://localhost/uwcadmin/agl/saveInfixData.php', req_data).then(function (res) {
                if (parseInt(res.mode) == '1') {
                    hmc.courseList[hmc.index].infixPrereq = item.infixPrereq;
                }

            });
        }

        function displayPreviousItem() {
            if (hmc.index > 0) {
                hmc.index--;
                hmc.course_info = hmc.courseList[hmc.index];

            }
        }

        function displayNextItem() {
            if (hmc.index < hmc.courseList.length) {
                hmc.index++;
                hmc.course_info = hmc.courseList[hmc.index];

            }
        }

    }

})();
