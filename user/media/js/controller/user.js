
// angular.module('myModule', ['ui.bootstrap']);
//var app = angular.module('app', ['ui.bootstrap']);

// app.service('DataSrv', ['$http', function($http){
		// return {
			// getData: function(url, params){
				// //example cache https://coderwall.com/p/40axlq
				// return $http.get(url, {cache: true} ).then(function(response){
	        		// return response.data.data;
	      		// });// $http//promise
			// },
		// };
// }]);


/**
 * we have this type of users
 * all_users = all available
 * grp_users = users belonging to the group
 */
app.controller('UserList', function($scope, $http, $window, $templateCache, DataSrv) {
	//TODO move urls to tags
	$scope.getUrl 	= "<?php echo URL::site('/subscription/get_users'); ?>";
	$scope.postUrl 	= "<?php echo URL::site('/subscription/put_users'); ?>"; 
	$scope.grp 		= $window.grp;
	//[{ id: '', username: '', email:'', selected: false,}];
	// $scope.all_users 	= [{}];
	// $scope.grp_users	= {};
	 
	DataSrv.getData($scope.getUrl+'/'+$scope.grp+'/grp').then(function(data){
		$scope.grp_users	= 	data;
	}); 
	DataSrv.getData($scope.getUrl+'/'+$scope.grp+'/ngrp').then(function(data){
		$scope.ngrp_users	= 	data;
		$scope.typeaheadlist = $scope.userData(data, 'username');
	}); //typeahead 
	
	$scope.inputUser = undefined;
 
  $scope.insertUsers = function() {
  	var url = $scope.postUrl+'/'+$scope.grp;
  	data = $.param({username: $scope.inputUser});
  	// TODO user: http.put
  	$http({method: 'POST', url: url, data: data, headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).
  	success(function(data, status) {
  		$scope.inputUser = '';
  		var index = $scope.typeheadlist.indexOf(
          $scope.typeahead.filter(function(t) {
            return t.id === user.id;
          })[0]);

      if (index !== -1) {
        $scope.typeaheadlist.splice(index, 1);
        $scope.grp_users.push(user);
      }
  	}).
  	error(function(data, status) {
  		alert('problems');
  	}); /*$http */
  };
  
  $scope.removeUsers = function(){	
  	var users = [];
  	$scope.grp_users.forEach(function(t){
  		if (t.selected) {
  			// users.push(t.id);
  			deleteItem(t);
  		};
  	});
  	// console.log(JSON.stringify(users));
  	// deleteItems(JSON.stringify(users));
  };
  
  function deleteItem(user){
  	$http({method: 'POST', url:'/subscription/delete_users/'+$window.grp, 
  		data: $.param({users: user.id}),
  		headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  		}
  	).success(function(){
  		// Find the index of an object with a matching id
      var index = $scope.grp_users.indexOf(
          $scope.grp_users.filter(function(t) {
            return t.id === user.id;
          })[0]);

      if (index !== -1) {
      	console.log($scope.ngrp_users);
        $scope.grp_users.splice(index, 1);
        $scope.ngrp_users.push(user);
        // console.log($scope.ngrp_users);
      }
  	}).error(function(err) {
      alert(err.message || "an error occurred");
    });
  };
  
  function deleteItems(users){
  	$http.post('/subscription/delete_users/'+$window.grp, { 
  		data: $.param({users: users}),
  		}
  	);
  };
  
  /**
   * filter users data
   * @prama data : array varaible 
   * @arg: fields ('id', 'email')
   * @return only wanted fileds
   */
  //TODO move to service
  $scope.userData = function(data) {
  	if (data == undefined) {
  		// data = [{username:'xxx'}];
  		//FIXME throw exception
  	};

  	// return ['users'];
  	var rtn = [];
  	if ( arguments.length > 1) {  		
		for (var j = 0; j < data.length; j++) {
			if (arguments.length > 2) {
				rtn2 = [];
				for (var i = 1; i < arguments.length; i++) {
					rtn2[arguments[i]] = $data[j][arguments[i]];
				}
				rtn.push(rtn2);
			} else {
				for (var i=1; i < arguments.length; i++) {			  
				  rtn.push(data[j][arguments[i]]);
				};
			}
		}
	}
  	return rtn;
  };
    
  //UI 
  $scope.insert = function($scope) {
  	$scope.tpl = '<tr>' 
  	+ '<td> <input name="$users[{{$id}}]" type="checkbox" ng-model="user.selected"> </td>'
  	+ '<td><span class="selected-{{user.selected}}">{{user.username}}</span></td>'
  	+ '</tr>';
  };
  
});//UserList
