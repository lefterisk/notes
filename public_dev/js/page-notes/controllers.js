'use strict';

/* Controllers */

angular.module('NotesApp.page-notes.controllers', [])
    .controller('NotesPageCtrl',['$scope', '$state', 'NotesListingData', 'Notes', 'toastr', function ($scope, $state, NotesListingData, Notes, toastr) {
        $scope.notes = NotesListingData.items;
        $scope.pagination = NotesListingData.pagination;

        $scope.goToItem = function (id) {
            $state.go('notes-edit', {id: id});
        };

        $scope.$watch('pagination.currentPage', function(newValue, oldValue) {
            if (typeof newValue != 'undefined' && newValue != oldValue) {
                var params = $state.params;
                params.page = (newValue > 1) ? newValue : null;
                $state.go($state.current.name, params, { notify:true, reload:true});
            }
        });
    }])
    .controller('NotesAddPageCtrl',['$scope', '$state','Notes', 'toastr', function ($scope, $state, Notes, toastr) {
        $scope.note = {
            title : '',
            text : ''
        };

        $scope.formErrors = [];

        $scope.submit = function (isValid) {

            if (isValid) {
                $scope.formErrors = [];

                Notes.post({},{
                        title : $scope.note.title,
                        text : $scope.note.text
                    },
                    function (data) {
                        $scope.formErrors = data.errors;
                        if (data.errors.length == 0) {
                            toastr.success('Saved Successfully');
                            $state.go('notes');
                        } else {
                            toastr.error('Oops...  The form has errors!');
                        }
                    },
                    function (data) {
                        toastr.error('Oops...  There was a problem!');
                    }
                )
            }
        }
    }])
    .controller('NotesEditPageCtrl',['$scope', '$state', 'NoteData', 'Notes', 'toastr', function ($scope, $state, NoteData, Notes, toastr) {
        $scope.note = {
            title : NoteData.note.title,
            text : NoteData.note.text
        };

        $scope.formErrors = [];

        $scope.submit = function (isValid) {

            if (isValid) {
                $scope.formErrors = [];

                Notes.put({id : $state.params.id, page: 1},{
                        title : $scope.note.title,
                        text : $scope.note.text
                    },
                    function (data) {
                        $scope.formErrors = data.errors;
                        if (data.errors.length == 0) {
                            toastr.success('Saved Successfully');
                            $state.go('notes');
                        } else {
                            toastr.error('Oops...  The form has errors!');
                        }
                    },
                    function (data) {
                        toastr.error('Oops...  There was a problem!');
                    }
                )
            }
        }
    }])
;