
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
  local: 'local',
  cuentas: 'cuentas'
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
	      if (!AuthService.isAuthenticated()) { 
	        // user is not allowed
	        $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
	      }else { 
	        // user is not logged in
          $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
	        event.preventDefault();
	      }
	    }
    }
  });
});


app.run(function(){
    /*
     * this swallows backspace keys on any non-input element.
     * stops backspace -> back
     */
    var rx = /INPUT|SELECT|TEXTAREA/i;

    $(document).bind("keydown keypress", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if(!rx.test(e.target.tagName) || e.target.disabled || e.target.readOnly ){
                e.preventDefault();
            }
        }
    });
});




function formatLocalDate() {

			    var now = new Date(),
			        tzo = -now.getTimezoneOffset(),
			        dif = tzo >= 0 ? '+' : '-',
			        pad = function(num) {
			            norm = Math.abs(Math.floor(num));
			            return (norm < 10 ? '0' : '') + norm;
			        };
			        
			    return now.getFullYear() 
			        + '-' + pad(now.getMonth()+1)
			        + '-' + pad(now.getDate());
};
