
var app = angular.module('app', ['ngRoute','xeditable', 'ui.bootstrap']);


app.run(function(editableOptions) {
  editableOptions.theme = 'bs3';
});



/*
app.constant('AUTH_EVENTS', {
  loginSuccess: 'auth-login-success',
  loginFailed: 'auth-login-failed',
  logoutSuccess: 'auth-logout-success',
  sessionTimeout: 'auth-session-timeout',
  notAuthenticated: 'auth-not-authenticated',
  notAuthorized: 'auth-not-authorized'
})

.constant('USER_ROLES', {
  all: '*',
  admin: 'admin',
  editor: 'editor',
  guest: 'guest'
});





app.run(function ($rootScope, AUTH_EVENTS, AuthService) {

  $rootScope.$on('$stateChangeStart', function (event, next) {
  
    var authorizedRoles = next.data.authorizedRoles;
    
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
    
  });

  
})*/