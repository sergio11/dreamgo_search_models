'use strict';

angular.module('app', ['ui.bootstrap', 'ui.router', 'ngAnimate', 'anim-in-out', 'ngTagsInput', 'angularFileUpload'])
    .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {

        $urlRouterProvider.otherwise("/home");

        $stateProvider
            .state('home', {
                url: "/home",
                templateUrl: "templates/home.html"
            })
            .state('add', {
                url: "/add",
                templateUrl: "templates/add-model.html"
            })
            .state('search', {
                url: "/search",
                templateUrl: "templates/search-models.html"
            })

    } ])
    .service('ModelsService', ['$http', function ($http) {

        //Save Tags for model
        this.saveTags = function (model, tags) {
            return $http.post('/modelos/web/index.php/api/v1/terms', { 'tags': tags })
                .then(function (response) {
                    var data = response.data;
                    if (!data.error && data.ids.length) {
                        return $http.post('/modelos/web/index.php/api/v1/models/' + model + '/tags', { 'tags': data.ids });
                    }

                });
        }
        //Get tags for model
        this.getTags = function (model) {
            return $http.get('/modelos/web/index.php/api/v1/models/' + model + '/tags');
        }
        //Get all Models
        this.getModels = function (start, count) {
            return $http.get('/modelos/web/index.php/api/v1/models/' + start + '/' + count);
        }
        //Get Total Models
        this.getCountModels = function () {
            return $http.get('/modelos/web/index.php/api/v1/models/count');
        }

    } ])
    .controller('addModelCtrl', ['$scope', 'ModelsService', 'FileUploader', function ($scope, ModelsService, FileUploader) {

        $scope.alerts = [];
        $scope.fileTags = {};
        $scope.uploader = new FileUploader({
            url: '/modelos/web/index.php/api/v1/models'
        });

        // Upload custom filter
        $scope.uploader.filters.push({
            name: 'xmlFilter',
            fn: function (item /*{File|FileLikeObject}*/, options) {
                var excel_mime_types = [
                    'application/vnd.ms-excel',
                    'application/msexcel',
                    'application/x-msexcel',
                    'application/x-ms-excel',
                    'application/x-excel',
                    'application/x-dos_ms_excel',
                    'application/xls',
                    'application/x-xls',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]
                return excel_mime_types.indexOf(item.type) >= 0;
                //return this.queue.length < 10;
            }
        });

        $scope.closeAlert = function (index) {
            $scope.alerts.splice(index, 1);
        };
        //Load Tags
        $scope.loadTags = function (key) {
            if ($scope.fileTags && $scope.fileTags[key]) {
                var model = $scope.fileTags[key];
                ModelsService.getTags(model.id).then(function (response) {
                    model.tags = response.tags;
                });
            }
        }

        //Save model tags
        $scope.saveTags = function (key) {
            if ($scope.fileTags && $scope.fileTags[key]) {
                var model = $scope.fileTags[key];
                //Save Model Tags
                ModelsService.saveTags(model.id, model.tags).then(function (response) {
                    model.dirty = false;
                });
            }
        }

        //Drop Model Tags
        $scope.dropTags = function (key) {
            if ($scope.fileTags && $scope.fileTags[key]) {
                var tags = $scope.fileTags[key].tags;
                tags.splice(0, tags.length);
            }
        }

        // File adding failed.
        $scope.uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
            var msg = null;
            if (filter.name == 'xmlFilter') {
                msg = "You can only upload files xls"
            }
            $scope.alerts.push({ type: 'danger', msg: msg });
        };
        //File upload success
        $scope.uploader.onCompleteItem = function (fileItem, response, status, headers) {
            $scope.fileTags[fileItem.$$hashKey] = { id: response.id, tags: [], dirty: false };
        };


        $scope.uploader.onAfterAddingFile = function (fileItem) {
            console.info('onAfterAddingFile', fileItem);
        };
        $scope.uploader.onAfterAddingAll = function (addedFileItems) {
            console.info('onAfterAddingAll', addedFileItems);
        };
        $scope.uploader.onBeforeUploadItem = function (item) {
            console.info('onBeforeUploadItem', item);
        };
        $scope.uploader.onProgressItem = function (fileItem, progress) {
            console.info('onProgressItem', fileItem, progress);
        };
        $scope.uploader.onProgressAll = function (progress) {
            console.info('onProgressAll', progress);
        };
        $scope.uploader.onSuccessItem = function (fileItem, response, status, headers) {
            console.info('onSuccessItem', fileItem, response, status, headers);
        };
        $scope.uploader.onErrorItem = function (fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
        };
        $scope.uploader.onCancelItem = function (fileItem, response, status, headers) {
            console.info('onCancelItem', fileItem, response, status, headers);
        };

        $scope.uploader.onCompleteAll = function () {
            console.info('onCompleteAll');
        };



    } ])
    .controller('searchCtrl', ['$scope', 'ModelsService', '$log', function ($scope, ModelsService, $log) {

        var itemsPerPage = 8;
        $scope.reverse = true;
        $scope.totalModels = 0;
        $scope.currentPage = 1;
        $scope.maxSize = 5;
        $scope.models = [];

        //Change Page
        $scope.pageChanged = function () {
            ModelsService.getModels(itemsPerPage * ($scope.currentPage - 1) + 1, itemsPerPage).then(function (response) {
                $scope.models = response.data;
            })
        };
        //Delete model
        $scope.deleteModel = function () {
               
        }

        ModelsService.getCountModels().then(function (response) {
            $scope.totalModels = response.data;
        })

        ModelsService.getModels(itemsPerPage * ($scope.currentPage - 1) + 1, itemsPerPage).then(function (response) {
            $scope.models = response.data;
        });

    } ]);
