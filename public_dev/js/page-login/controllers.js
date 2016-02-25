'use strict';

/* Controllers */

angular.module('NotesApp.page-login.controllers', [])
    .controller('LoginPageCtrl',['$scope', '$state', '$http', 'toastr', function ($scope, $state, $http, toastr) {
        $scope.user = {
            username : '',
            password : ''
        };

        $scope.formErrors = [];

        $scope.submit = function (isValid) {

            if (isValid) {
                $scope.formErrors = [];

                var request = {
                    method: 'POST',
                    url: '/api/auth',
                    data: {
                        username   : $scope.user.username,
                        password   : $scope.user.password
                    }
                };

                $http(request).success(function(data, status, headers, config) {
                        $scope.formErrors = data.errors;
                        if (data.errors.length == 0) {
                            toastr.success('Logged in successfully');
                            $state.go('notes');
                        }
                    })
                    .error(function(data, status, headers, config) {
                        $scope.formErrors = data.errors;
                    });
            }
        }
    }])
;