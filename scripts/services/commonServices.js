app.factory('AlertService', ['$rootScope', '$timeout', function($rootScope, $timeout) {

    var alertService;
    alertService = void 0;
    $rootScope.alerts = [];
    $rootScope.loading = null;
      
	return alertService = {
        add: function(type, msg, timeout) {
          $rootScope.alerts.push({
            type: type,
            msg: msg,
            close: function() {
              return alertService.closeAlert(this);
            }
          });
          if (timeout) {
            $timeout((function() {
              alertService.closeAlert(this);
            }), timeout);
          }
        },
        closeAlert: function(alert) {
          return this.closeAlertIdx($rootScope.alerts.indexOf(alert));
        },
        closeAlertIdx: function(index) {
          return $rootScope.alerts.splice(index, 1);
        },
        clear: function() {
          $rootScope.alerts = [];
        },
        loading: function(txt) {
          alertService.closeLoading();
          if (txt === void 0) {
            txt = 'Cargando ...';
          }
          return $rootScope.loading = this.add("warning", txt);
        },
        closeLoading: function() {
          alertService.closeAlert($rootScope.loading);
          return $rootScope.loading = null;
        }
      };
    }
  ]);