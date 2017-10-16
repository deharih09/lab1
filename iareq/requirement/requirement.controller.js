(function () {
    'use strict';

    angular
            .module('cpApp')
            .controller('RequirementController', RequirementController);

    RequirementController.$inject = ['UserService', 'CourseService', '$rootScope', '$http'];
    function RequirementController(UserService, CourseService, $rootScope, $http) {
        var hmc = this;

        hmc.major_requirements = [];
        hmc.user = null;

        hmc.category_info_default = {type: 1, category: 1, notes : -1, num_courses : -1, max_credits : -1, min_credits : -1, courses : -1, 
                            choices : -1, id : -1, units : -1, req_id: -1};
        hmc.category = {min_credits: 0, courses: -1};
        hmc.category_info = angular.copy(hmc.category_info_default);
        hmc.tmp_item = {};
        //hmc.info = {id: 6};
        hmc.state = {id : -1};
        hmc.index = 0;
        hmc.max_index = 0;
        hmc.req_types = [{id: 1, name: 'Required course(s)'},
            {id: 0, name: 'Select one course from the  course list'},
            {id: 2, name: 'Select more than one  course from the  course list'},
            {id: 3, name: 'Need a  custom course filter'}

        ];

        hmc.req_category = 'major';
        hmc.rec_saved = false;
        hmc.courselist_error = false;
        hmc.save = save;
        hmc.requirement_labels = [];
        hmc.requirement_choices = {};
        hmc.getRequirementList = getRequirementList;
        hmc.getRequirementLabels = getRequirementLabels;
        hmc.displayPreviousItem = displayPreviousItem;
        hmc.displayNextItem = displayNextItem;
        var url = 'http://localhost/iareq-api/';
       hmc.req_index = 0;

        initController();

        function initController() {
            loadCurrentUser();
            getCategoryList('major');
            
            //getRequirementList('category', hmc.info);

        }

        function loadCurrentUser() {
            hmc.user = $rootScope.globals.currentUser;
        }

        function getRequirementLabels(category, m) {
            hmc.requirement_labels = [];
            
            var action = url + "requirement/getCategoryList/"+category+"/"+m.id;
            $http({
                method: 'get',
                url: action ,

            }).success(function (data, status, headers, config) {
                hmc.requirement_labels = data;
                console.log(data)
            });
        }
        function  getRequirementList(category, m, index) {
          console.log(m)
            var action = url + "requirement/getRequirements/"+category + "/" + m.id;
            $http({
                method: 'get',
                url: action ,
    
            }).success(function (data) {
                console.log(data)
                if (data.length>0){
                hmc.category_requirements = data;
                hmc.category_info = hmc.category_requirements[0];
                hmc.category = hmc.category_requirements[0];
                hmc.max_index = hmc.category_requirements.length - 1;
               // hmc.info = hmc.major[index];
                hmc.state.id = hmc.category.state;
                
                if (hmc.category.courses == ' , ')
                    hmc.category.courses = -1;
                console.log(hmc.category_info);
            } else {
                // reset requirements
                hmc.category_requirements = [];
                hmc.category_info = angular.copy(hmc.category_info_default);
                hmc.category = angular.copy(hmc.category_info_default);
            }
                hmc.rec_saved = false;
                hmc.req_index = 0;
            });
        }
        ;
        function getCategoryList(category) {
           var action = url+'requirement/getCategoryList/';
            $http.get(action + category).then(function(res){
                hmc[category] = res.data;
                //hmc.info = res.data[0];
            });
        }
        

        function save() {
            var item = hmc.category_info;
            if (hmc.category_info.category == 2){
                   item.choices = hmc.requirement_choices.req_id;
                   item.type = 2;
               } else {
                   item.choices = -1;
                   item.type = 1;
               }
                console.log(item);
            var req_data = {category : hmc.req_category, category_id: hmc.info.id, label: item.label, min_credits: item.min_credits, max_credits: item.max_credits,
                courses: item.courses, state: hmc.state.id, units: item.units, notes: item.notes, req_id: item.req_id, num_courses : item.num_courses,
                type : item.type, choices : item.choices, id : item.id};
            
 
            
          $http.post(url + 'degree/saveRequirement', req_data).then(function (res) {
              console.log(res.data)
                if(typeof res.data.mode !== 'undefined' ){
                   if (parseInt(res.data.mode) === 1){
                    hmc.rec_saved = true;
                    hmc.courselist_error = false;
                   } else if (parseInt(res.data.mode) === -2){
                       hmc.courselist_error = true;
                   }
                }
            });
            
        }

        function displayPreviousItem() {
            if (hmc.index > 0) {
                hmc.index--;
                hmc.category = hmc.category_requirements[hmc.index];
                if (hmc.category.courses == ' , ')
                    hmc.category.courses = -1;
            }
        }

        function displayNextItem() {
            if (hmc.category_requirements.length === 0){
                hmc.category_info = angular.copy(hmc.category_info_default);
            } else if (hmc.req_index < hmc.max_index) {
                hmc.req_index++;
                hmc.category_info = hmc.category_requirements[hmc.req_index];
                hmc.state.id = hmc.category_info.state;
                var courses = hmc.category_info.courses;
                if (courses == ' , ' || courses.length === 0)
                    hmc.category_info.courses = -1;
                
            }
            hmc.rec_saved = false;
        }

    }

})();
