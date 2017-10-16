(function () {
    'use strict';

    angular
        .module('cpApp')
        .factory('CourseService', CourseService);

    CourseService.$inject = ['$http'];
    function CourseService($http) {
        var service = {};
		var url = 'http://localhost/iareq/';

        service.TakenCourses =TakenCourses;
		service.CompletedCourses =CompletedCourses;
		service.CourseList = CourseList;
		service.GenedCourseList = GenedCourseList;
		service.GenedAreaList = GenedAreaList;
        service.AreaList= AreaList;
		service.CsElectiveList= CsElectiveList;
		service.DanceElectiveList= DanceElectiveList;
		service.SubjectList = SubjectList;
		service.SaveCompletedCourses = SaveCompletedCourses;
		service.RemoveCompletedCourses = RemoveCompletedCourses;
		service.GenedElectiveList = GenedElectiveList;
		service.getGenedElectiveCourseList = getGenedElectiveCourseList;


        return service;

        function TakenCourses(userid) {
            return $http.get(url + 'course/takenCourses/'+userid).then(handleSuccess, handleError('Error obtaining data'));
        }

        function CompletedCourses(userid) {
            return $http.get(url + 'course/completedCourses/'+userid).then(handleSuccess, handleError('Error obtaining data'));
        }

        function CourseList(course) {
            return $http.post(url +'course/courseList', {subject : course.subject}).then(handleSuccess, handleError('Error getting subjects'));
        }

        function GenedCourseList(course) {
            return $http.post(url +'course/genedcourseList', {subject: course.subject, category : course.category}).then(handleSuccess, handleError('Error obtaining Gened course list'));
        }

        function AreaList() {
            return $http.get(url +'course/areaList').then(handleSuccess, handleError('Error obtaining area list'));
        }

        function GenedAreaList() {
            return $http.get(url +'course/getGenedSubjectAreaList').then(handleSuccess, handleError('Error obtaining Gened area list'));
        }

        function SubjectList() {
            return $http.get(url +'course/subjectList').then(handleSuccess, handleError('Error updating user'));
        }

        function SaveCompletedCourses(courseInfo) {
            return $http.post(url +'course/saveCompletedCourse', courseInfo).then(handleSuccess, handleError('Error saving courses'));
        }
		
		function RemoveCompletedCourses(courseInfo) {
            return $http.post(url +'course/removeCompletedCourse', courseInfo).then(handleSuccess, handleError('Error removing courses'));
        }
		
		function CsElectiveList() {
            return $http.get('agl/getCsElectiveCourses.php').then(handleSuccess, handleError('Error obtaining area list'));
        }
		
		function DanceElectiveList() {
            return $http.get(url+'agl/getDanceElectiveCourses.php').then(handleSuccess, handleError('Error obtaining area list'));
        }
		
		function GenedElectiveList() {
            return $http.get(url+'agl/getGenedElectiveAreaList.php').then(handleSuccess, handleError('Error obtaining area list'));
        }
		
		function getGenedElectiveCourseList(subject) {
            return $http.post(url+'agl/getGenedElectiveCourseList.php', {subject : subject}).then(handleSuccess, handleError('Error obtaining area list'));
        }

		
	
		

        // private functions

        function handleSuccess(res) {
            return {success : true, data : res.data};
        }

        function handleError(error) {
            return function () {
                return { success: false, message: error };
            };
        }
    }

})();
