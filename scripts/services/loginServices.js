
app.factory('AuthService', function ($http, Session) {
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
    
    isAuthenticated: function () {
      return !!Session.getUserId();
    },
    
    isAuthorized: function (authorizedRoles) {
      if (!angular.isArray(authorizedRoles)) {
        authorizedRoles = [authorizedRoles];
      }


      var isAuthe = this.isAuthenticated();
      var isAutho =  (authorizedRoles.indexOf(Session.getUserRole())!= -1);
      
      return isAuthe && isAutho;

    }
  };
})




.service('Session', function (AlertService, $rootScope) {
	
	  this.create = function (userId, userName, userRole) {
	  
		  if(typeof(Storage)!=="undefined"){
		  	sessionStorage.userId = userId;
		  	sessionStorage.userName=userName;
		  	sessionStorage.userRole=userRole;
			
		
		  }else{
		  	AlertService.add('danger', "El navegador no soporta sessionStorage", 5000);;
		  }
	  };
	  
	  this.destroy = function () {
		  sessionStorage.userId = null;
		  sessionStorage.userName = null;
		  sessionStorage.userRole = null;
		  
	  };
	  
	   
	  this.getUserName= function(){
	  	var name = (sessionStorage.userName != 'null')?sessionStorage.userName:''; 
		  return name;
	  };
	  
	  this.getUserId= function(){
		  return sessionStorage.userId;
	  };
	  this.getUserRole= function(){
		  return sessionStorage.userRole;
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











