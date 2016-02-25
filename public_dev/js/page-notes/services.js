'use strict';

/* Services */

angular.module('NotesApp.page-login.services', [])
    .factory('Notes', ['$resource', function ($resource) {
        return $resource('/api/note/page/:page/:id',{id: '@id'}, {'put': {method: 'PUT', isArray: false}, 'get': {method: 'GET'}, 'post': { method: 'POST', isArray: false}});
    }])
    .factory('NotesListingData', ['$q','Notes', function ($q,Notes) {
        return function (stateparams) {
            var delay = $q.defer();
            Notes.get({
                page : (stateparams.page) ? stateparams.page : '1'
            }, function (data) {
                delay.resolve(data);
            }, function () {
                delay.reject('Unable to fetch data');
            });
            return delay.promise;
        };
    }])
    .factory('NoteData', ['$q','Notes', function ($q,Notes) {
        return function (stateparams) {
            var delay = $q.defer();
            Notes.get({
                id: stateparams.id,
                page: 1
            },function (data) {
                delay.resolve(data);
            }, function () {
                delay.reject('Unable to fetch data');
            });
            return delay.promise;
        };
    }])
;