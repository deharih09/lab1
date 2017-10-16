"use strict"
$(document).ready(function(){
var base_url = 'http://cs.uww.edu/beta/coreapi/';

/*   Example 
  $('#customerlist').on('click', function(){
	// define controller, method, and parameter
	var controller = 'customer';
	var action = 'getCustomerList';
	var parameter = '';
	var method = 'post';
	// display data
	processRequest(controller, action, parameter, method);
  });
*/
  // Private functions
   function processRequest(controller, action, parameter, method){
	var url = base_url + controller;
	if (typeof action !== 'undefined' && action !== '')
		url +=   '/' + action ;
	if (typeof parameter !== 'undefined' && parameter !== '')
		url += '/'+ parameter;
	$.ajax({
		method : method,
		url : url,
		dataType : 'json',
		success : function(response){
			//console.log(response);
	  //  customer list  is a property of the response object
	  // send the customer list to the displayResultSet() method
		var customer_list = response.customers;
			displayResultSet(customer_list);
		},
		error : function(response){
		}
	});
   }

  function displayResultSet(data){
	if (typeof data != 'undefined' && data.length>0){
		var msg = '<table class="table">';
		for (var i=0; i<data.length; i++){
			msg += "<tr>";
			// Obtain properties of each object. Properties are the column labels of the result set.
			var columns = Object.keys(data[i]);
			for (var j=0; j<columns.length; j++) 
				msg += "<td>" + data[i][columns[j]] + "</td>";
			msg += "</tr>";
		}
		$('#customer-data').html(msg + "</table>");
	}
  }

});
