'use strict';


// IE8 fix - be careful not to use console.warn() - it will cause IE8 to silently not run angular.
var console = console || {};

// Globally available cmt dependency array so we can add custom components
var dependencies = [
    'ui.router',
    'ngAnimate',
    'ngResource',
    'ngTouch',
    'ngSanitize',
    'ngCookies',
    'ui.bootstrap', //bootstrap - angular
    'http-auth-interceptor',
    'toastr',
    'angular-loading-bar',

    'NotesApp.controllers',

    'NotesApp.page-login',
    'NotesApp.page-notes'
];

// Declare app level module which depends on filters, and services
angular.module('NotesApp', dependencies)
    .config(['$stateProvider','$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.when("/", "/notes");
        $urlRouterProvider.when("", "/notes");
    }])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.html5Mode(true);
    }])
    .config(function (toastrConfig) {
        angular.extend(toastrConfig, {
            allowHtml: true,
            positionClass: 'toast-bottom-right'
        });
    })
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function(obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for(name in obj) {
                value = obj[name];

                if(value instanceof Array) {
                    for(i=0; i<value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }

                    if (value.length == 0) {
                        query += encodeURIComponent(name) + '=' + '&';
                    }
                }
                else if(value instanceof Object) {
                    for(subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];
    }])
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    }])
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    }])
    .run(['$rootScope', '$state', function ($rootScope, $state) {
        //IE8 fix for non-existent indexOf extension
        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function (obj, start) {
                for (var i = (start || 0), j = this.length; i < j; i++) {
                    if (this[i] === obj) {
                        return i;
                    }
                }
                return -1;
            }
        }

        //Listener for the event
        $rootScope.$on('event:auth-loginRequired', function(event, rejection) {
            if (rejection.statusText == 'Unauthorized') {
                $state.go('login');
            }
        });
    }]);