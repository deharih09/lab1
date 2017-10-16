(function () {
    'use strict';

    angular
            .module('cpApp')
            .controller('GenedController', GenedController);

    GenedController.$inject = ['UserService', 'CourseService', '$rootScope', '$http'];
    function GenedController(UserService, CourseService, $rootScope, $http) {
        var hmc = this;

        hmc.gened_requirements = [];
        hmc.user = null;

        hmc.gened_info_default = {type: 1, category: 1, notes : -1, num_courses : -1, max_credits : -1, min_credits : -1, courses : -1, 
                            choices : -1, sub_state: -1};
        hmc.gened = {min_credits: 0, courses: -1};
        hmc.gened_info = angular.copy(hmc.gened_info_default);
        hmc.tmp_item = {};
        hmc.info = {id : -1};
        hmc.state = {id : -1};
        hmc.index = 0;
        hmc.max_index = 0;
        hmc.req_types = [{id: 1, name: 'Required course(s)'},
            {id: 0, name: 'Select one course from the  course list'},
            {id: 2, name: 'Select more than one  course from the  course list'},
            {id: 3, name: 'Need a  custom course filter'}

        ];
        hmc.req_subtypes = [
            {id: 0, name: 'Select one course from the  list'},
            {id: 1, name: 'Select more than one  course from the   list'},
            {id: 2, name: 'Need a  custom filter'}

        ];

        hmc.req_category = 1;
        //hmc.rec_subcategory = false;
        hmc.save = save;
        hmc.requirement_labels = [];
        hmc.requirement_choices = {};
        hmc.getRequirementList = getRequirementList;
        hmc.getRequirementLabels = getRequirementLabels;
        hmc.displayPreviousItem = displayPreviousItem;
        hmc.displayNextItem = displayNextItem;


        initController();

        function initController() {
            loadCurrentUser();
            //getCategoryList('gened');
            
            //getRequirementList('gened', hmc.info);

        }

        function loadCurrentUser() {
            hmc.user = $rootScope.globals.currentUser;
        }

        function getRequirementLabels() {
            hmc.requirement_labels = [];
            
            //var action = 'http://localhost:8080/course/'+m.id;
            var action = "http://localhost/uwcfrm/requirement/getGenedRequirementLabels/";
            $http({
                method: 'POST',
                url: action ,
               
               
               
            }).success(function (data, status, headers, config) {
                hmc.requirement_labels = data;
                console.log(data)
            });
        }
        function  getRequirementList(category, m) {
            console.log(m)
            var requirement = {category: category, category_id: m.id};
            //var action = 'http://localhost:8080/course/'+m.id;
            var action = "http://localhost/uwcfrm/requirement/getRequirements/";
            $http({
                method: 'POST',
                url: action ,
                data: requirement,
               
               
            }).success(function (data, status, headers, config) {
                console.log(data);
              /*var c_data =JSON.parse("'"+data+"'");
              //console.log(c_data);*/
                if (data.length>0){
                  /*  var dcopy = angular.copy(data);
                    for (var i=0; i<dcopy.length; i++){
                        
                        dcopy[i] = JSON.parse(dcopy[i]);
                        
                    }
                    console.log(dcopy)*/
                hmc.gened_requirements = data.requirements;
                hmc.gened = hmc.gened_requirements[0];
                hmc.max_index = hmc.gened_requirements.length - 1;
                hmc.info = {id: hmc.gened.pk};
                hmc.state.id = hmc.gened.state;
                if (hmc.gened.courses == ' , ')
                    hmc.gened.courses = -1;
                console.log(hmc.gened_requirements);
            }

            });
        }
        ;
        function getCategoryList(category) {
           // var action = 'http://localhost:8080/course';
           var action = 'http://localhost/uwcfrm/degree/getCategoryList/';
            $http.get(action + category).then(function(res){
                       // hmc[category+'s'] = res.data;
           // $http.get(action).then(function (res) {
                /*console.log(res.data)*/
               // if (res.data.length>0){
                    /*var dcopy = angular.copy(res.data);
                    for (var i=0; i<dcopy.length; i++){
                        
                        dcopy[i] = JSON.parse(dcopy[i]);
                        
                    
                    }
                }*/
                hmc[category] = res.data;
                hmc.info = res.data[0];

            });
        }

        function save() {
            var item = hmc.gened_info;
            console.log(item);
            if (hmc.gened_info.category == 2){
                   item.choices = hmc.requirement_choices.req_id;
                   item.type = 2;
               } else {
                   item.choices = -1;
                   item.type = hmc.gened_info.type;
               }
                console.log(item);
            var req_data = {category: 'gened', gened_id: hmc.info.id, label: item.label, min_credits: item.min_credits, max_credits: item.max_credits,
                courses: item.courses, state: hmc.state.id, units: item.units, notes: item.notes, req_id: item.req_id, num_courses : item.num_courses,
                type : item.type, choices : item.choices, sub_state: item.sub_state};
            
            console.log(req_data);
            hmc.gened_info = angular.copy(hmc.gened_info_default);
           $http.post('http://localhost/uwcadmin/agl/saveRequirementData.php', req_data).then(function (res) {
                console.log(res);
                hmc.gened_info = angular.copy(hmc.gened_info);
            });
        }

        function displayPreviousItem() {
            if (hmc.index > 0) {
                hmc.index--;
                hmc.gened = hmc.gened_requirements[hmc.index];
                if (hmc.gened.courses == ' , ')
                    hmc.gened.courses = -1;
            }
        }

        function displayNextItem() {
            if (hmc.index < hmc.gened_requirements.length) {
                hmc.index++;
                hmc.gened = hmc.gened_requirements[hmc.index];
                if (hmc.gened.courses == ' , ')
                    hmc.gened.courses = -1;
            }
        }

    }

})();
