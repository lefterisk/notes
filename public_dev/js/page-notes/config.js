'use strict';

angular.module(
    "NotesApp.page-notes",
    [
        "NotesApp.page-notes.controllers",
        "NotesApp.page-notes.services"
    ]
)
    .config(['$stateProvider', function ($stateProvider) {
        $stateProvider.state('notes', { url: '/notes?{page}', templateUrl: '/tpls/notes-listing.html', controller: 'NotesPageCtrl',
            resolve: {
                NotesListingData: function (NotesListingData, $stateParams) {
                    return NotesListingData($stateParams);
                }
            }
        })
        .state('notes-add', { url: '/notes/add', templateUrl: '/tpls/notes-add.html', controller: 'NotesAddPageCtrl'})
        .state('notes-edit', { url: '/notes/:id', templateUrl: '/tpls/notes-edit.html', controller: 'NotesEditPageCtrl',
            resolve: {
                NoteData: function (NoteData, $stateParams) {
                    return NoteData($stateParams);
                }
            }
        })
    }])
;