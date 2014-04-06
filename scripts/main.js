
var app = angular.module('app', ['ngRoute','xeditable', 'ui.bootstrap']);



app.constant('AUTH_EVENTS', {
  loginSuccess: 'auth-login-success',
  loginFailed: 'auth-login-failed',
  logoutSuccess: 'auth-logout-success',
  sessionTimeout: 'auth-session-timeout',
  notAuthenticated: 'auth-not-authenticated',
  notAuthorized: 'auth-not-authorized'
});



app.constant('USER_ROLES', {
  all: '*',
  admin: 'admin',
  taller: 'taller',
  local: 'local'
});



app.run(function(editableOptions) {
  editableOptions.theme = 'bs3';
});





app.run(function ($rootScope, $route, $location, AUTH_EVENTS, USER_ROLES, AuthService) {

  $rootScope.$on('$locationChangeStart', function (event, next) {
  
    var nextPath = $location.path();
    var nextRoute = $route.routes[nextPath];
    
    if((typeof(nextRoute) !== "undefined")&&(nextRoute.auth.needAuth)){
	
	    var authorizedRoles = nextRoute.auth.authorizedRoles;

	    if (!AuthService.isAuthorized(authorizedRoles)) {
	      
	      event.preventDefault();
	      if (AuthService.isAuthenticated()) {
	        // user is not allowed
	        $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
	      } else {
	        // user is not logged in
	        $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
	      }
	      
	    }
	}
  });

  
})