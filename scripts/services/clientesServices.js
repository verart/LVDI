
app.service('clientesService', ['$http','$q','pendingRequests', function ($http,$q,pendingRequests) {
        return {
        	//CLIENTES ************************************************
            clientes:function(p,f) {
	            var canceller = $q.defer();
				pendingRequests.add({
					url: dir_api + '/clientes/index',
					canceller: canceller
				});
				var promise = $http({
	            	method: 'POST',
	            	url: dir_api + '/clientes/index',
	            	data: $.param({pag:p, filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            promise.finally(function() {
      				pendingRequests.remove(url);
    			});
				return promise; 
            },
            //CLIENTE ****************************************************
            cliente:function(idCl) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientes/'+idCl+'/show',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            //ADDCLIENTE ****************************************************
            addCliente:function (cliente) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/clientes/create',
	            	data: $.param(cliente),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            //EDITCLIENTE ****************************************************
            editCliente: function(cliente){ 
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/clientes/update',
	            	data: $.param(cliente),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            },
            //DELETECLIENTE ****************************************************
            deleteCliente:function (id) { 
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/clientes/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            //GETMAILS ****************************************************
            getMails:function() {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientes/mails',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            //LIST ****************************************************
            list: function(term) {
	        var request  = dir_api + '/clientes/'+term+'/list';;
	        return $http.get(request).then(function(response) {
	            var _list = [];
	            for(var key in response.data.DATA) {
	              _list.push(response.data.DATA[key]);
	            }
	            return _list;
	          });
	      	}
        }
}]);