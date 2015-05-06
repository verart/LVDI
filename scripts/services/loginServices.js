
app.factory('AuthService', function ($http, Session, AlertService) {
  return {
    
    login: function (credentials) {

      return $http({
	                method: 'POST', 
	                url: dir_api + '/sesion',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	                data: $.param(credentials),
	          }).then(
		          function (res) { 
		          	Session.create(res.data.DATA.id, res.data.DATA.nombre, res.data.DATA.perfil); 
		          }
		      );
    },

    logout: function () {   
      return $http({
	                method: 'GET', 
	                url: dir_api + '/sesion/logout',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	          }).then(
		          function (res) {
		          	Session.destroy(); 
		          	AlertService.add('success', res.data.MSG, 1000);
		          }
		      );
    },

    isAuthenticated: function () {
      return !!Session.getUserId();
    },
    
    isAuthorized: function (authorizedRoles) { 
    	if (!angular.isArray(authorizedRoles))
        	authorizedRoles = [authorizedRoles];

	    var isAuthe = this.isAuthenticated();
    	var isAutho =  (authorizedRoles.indexOf(Session.getUserRole())!= -1);
    	return isAuthe && isAutho;
    }

  };
})




.service('Session', function (AlertService, $rootScope) {
	
	this.create = function (userId, userName, userRole) {
		if(typeof(Storage)!=="undefined"){
		  	localStorage.userId = userId;
		  	localStorage.userName=userName;
		  	localStorage.userRole=userRole;
		}else
		  	AlertService.add('danger', "El navegador no soporta sessionStorage", 5000);	  
	};
	  
	  this.destroy = function () {
		  localStorage.userId = null;
		  localStorage.userName = null;
		  localStorage.userRole = null;
		  
	  };
	  
	   
	  this.getUserName= function(){
	  	var name = (localStorage.userName != 'null')?localStorage.userName:''; 
		  return name;
	  };
	  
	  this.getUserId= function(){
		  return localStorage.userId;
	  };
	  this.getUserRole= function(){
		  return localStorage.userRole;
	  };
	  
	  return this;
	 
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



// This service keeps track of pending requests
.service('pendingRequests', function() {
	var pending = [];
	this.get = function() {
		return pending;
	};
	this.add = function(request) {
		pending.push(request);
	};
	this.remove = function(request) {
		pending = _.filter(pending, function(p) {
			return p.url !== request;
		});
	};
	this.cancelAll = function() {
		angular.forEach(pending, function(p) {
			p.canceller.resolve();
		});
		pending.length = 0;
  	};
})

// This service wraps $http to make sure pending requests are tracked 
.service('httpService', ['$http', '$q', 'pendingRequests', function($http, $q, pendingRequests) {

	this.get = function(url) {
    	var canceller = $q.defer();
    	pendingRequests.add({
    		url: url,
    		canceller: canceller
    	});
    	
    	//Request gets cancelled if the timeout-promise is resolved
    	var requestPromise = $http.get(url, { timeout: canceller.promise });
    	
    	//Once a request has failed or succeeded, remove it from the pending list
    	requestPromise.finally(function() {
    		pendingRequests.remove(url);
    	});
    	
    	return requestPromise;
	}
}]);