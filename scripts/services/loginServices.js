
app.factory('AuthService', function ($http, Session) {
  return {
    login: function (credentials) {
      
      return $http({
	                method: 'POST', 
	                url: dir_api + '/sesion',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	                data: $.param(credentials),
	          }).then(
		          function (res) { Session.create(res.id, res.userid, res.role); }
		      );
    },
    isAuthenticated: function () {
      return !!Session.userId;
    },
    isAuthorized: function (authorizedRoles) {
      if (!angular.isArray(authorizedRoles)) {
        authorizedRoles = [authorizedRoles];
      }
      return (this.isAuthenticated() &&
        authorizedRoles.indexOf(Session.userRole) !== -1);
    }
  };
})




.service('Session', function () {

  this.create = function (sessionId, userId, userRole) {
    this.id = sessionId;
    this.userId = userId;
    this.userRole = userRole;
  };
  this.destroy = function () {
    this.id = null;
    this.userId = null;
    this.userRole = null;
  };
  return this;
  
})




.controller('ApplicationController', 
	
	function ($scope, USER_ROLES, AuthService) {
  
		$scope.currentUser = null;
		$scope.userRoles = USER_ROLES;
		$scope.isAuthorized = AuthService.isAuthorized;
})






.config(function ($httpProvider) {  

	$httpProvider.interceptors.push([    
		'$injector',    
		function ($injector) {      
			return $injector.get('AuthInterceptor');
	}]);
})



.factory('AuthInterceptor', function ($rootScope, $q, AUTH_EVENTS) {

  return {
  	responseError: function (response) {
      if (response.status === 401) {
      	$rootScope.$broadcast(AUTH_EVENTS.notAuthenticated,response);
      }
      if (response.status === 403) {
        $rootScope.$broadcast(AUTH_EVENTS.notAuthorized,response);
      }
      if (response.status === 419 || response.status === 440) {
      	$rootScope.$broadcast(AUTH_EVENTS.sessionTimeout,response);
      }
      return $q.reject(response);
    }
  };
})




.directive('formAutofillFix', function ($timeout) {

  return function (scope, element, attrs) {
    element.prop('method', 'post');
    if (attrs.ngSubmit) {
      $timeout(function () {
        element
          .unbind('submit')
          .bind('submit', function (event) {
            event.preventDefault();
            element
              .find('input, textarea, select')
              .trigger('input')
              .trigger('change')
              .trigger('keydown');
            scope.$apply(attrs.ngSubmit);
          });
      });
    }
  };

});


