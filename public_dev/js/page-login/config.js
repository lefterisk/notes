'use strict';

angular.module(
    "NotesApp.page-login",
    [
        "NotesApp.page-login.controllers",
        "NotesApp.page-login.services"
    ]
)
    .config(['$stateProvider', function ($stateProvider) {
        $stateProvider.state('login', { url: '/login', templateUrl: '/tpls/login.html', controller: 'LoginPageCtrl'})
    }])
;