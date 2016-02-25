'use strict';

/* Services */

angular.module('NotesApp.page-notes.services', [])
    .factory('Context', ['$resource', function ($resource) {
        return $resource('/cms-api/context/:contextId',{contextId: '@contextId'}, {'put': {method: 'PUT', isArray: false}, 'get': {method: 'GET'}, 'post': { method: 'POST', isArray: false}});
    }])
    .factory('ContextListingData', ['$q','Context', function ($q,Context) {
        return function () {
            var delay = $q.defer();
            Context.get(function (data) {
                delay.resolve(data);
            }, function () {
                delay.reject('Unable to fetch data');
            });
            return delay.promise;
        };
    }])
;