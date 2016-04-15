'use strict';


angular.module('app', ['ui.bootstrap', 'ui.router', 'ngAnimate', 'anim-in-out', 'ngTagsInput', 'angularFileUpload', 'oitozero.ngSweetAlert', 'flock.bootstrap.material'])
    .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {

        $urlRouterProvider.otherwise("/home");

        $stateProvider
            .state('home', {
                url: "/home",
                templateUrl: "templates/home.html"
            })
            .state('add', {
                url: "/add",
                templateUrl: "templates/add-model.html",
                controller: "addModelCtrl"
            })
            .state('search', {
                url: "/search",
                templateUrl: "templates/search-models.html",
                controller: "searchCtrl"
            })
            .state('predictionModel', {
                url: "/prediction-model",
                templateUrl: "templates/prediction-model.html",
                controller: "predictionCtrl"
            })
            .state('optimizationModel', {
                url: "/optimization-model",
                templateUrl: "templates/optimization-model.html",
                controller: "optimizationCtrl"
            })

    } ])
    .service('X2JS', function(){ return new X2JS() })
    .service('ModelsService', ['$http', function ($http) {

        //Save Tags for model
        this.saveTags = function (model, tags) {
            return $http.post('/models/public/api.php/terms', { 'tags': tags })
                .then(function (response) {
                    var data = response.data;
                    if (!data.error && data.ids.length) {
                        return $http.post('/models/public/api.php/models/' + model + '/tags', { 'tags': data.ids });
                    }

                });
        }

        //Delete Tags for model
        this.deleteTags = function(model, tags){
            return $http.delete('/models/public/api.php/models/' + model + '/tags/'+ tags);
        }

        //Get tags for model
        this.getTags = function (model) {
            return $http.get('/models/public/api.php/models/' + model + '/tags');
        }
        //Get all Models
        this.getModels = function (start, count, tags, orderBy) {
            return $http.get('/models/public/api.php/models/' + start + '/' + count, {
                params:{
                    tags: tags,
                    orderBy: orderBy
                }
            });
        }
        //Get Total Models
        this.getCountModels = function () {
            return $http.get('/models/public/api.php/models/count');
        }
        
        this.deleteModel = function(idModel){
            return $http.delete('/models/public/api.php/models/'+idModel);
        }

    } ])
    .service('TermsService', ['$http', function($http){
        
        this.getMatchingTerms = function(text){
            return $http.get('/models/public/api.php/terms/' + text);
        }
        
    }])
    .directive('datatable', function(){
        return {
            restrict: 'E',
            replace: true,
            scope: {
              source: '=',
              noDataMsg: '@',
              addBtnText: '@',
              headers: '='
            },
            controller: ['$scope', function($scope){
                
                console.log("Data Table controller ...");
                
                $scope.formInputs = {};
                $scope.addMode = false;
                
                //Return Index Row.
                var _getIndexRow = function(id){
                    return $scope.source.map(function(item){
                        return item.id;
                    }).indexOf(id);
                }
                
                //Edit row
                $scope.edit = function(item){
                    item.edit = true;
                    $scope.formInputs[item.id]  = angular.copy(item);
                }
                //Remove row.
                $scope.remove = function(id){
                    var idx = _getIndexRow(id);
                    if(idx >= 0){
                        $scope.source.splice(idx, 1);
                    }
                }
                // Save new row.
                $scope.save = function(id){
                    var idx = _getIndexRow(id);
                    var item = $scope.formInputs[id];
                    if(idx >= 0){
                        delete item.edit;
                        $scope.source[idx] = angular.copy(item);
                    }else{
                        $scope.source.push(angular.copy(item));
                        $scope.addMode = false;
                    }

                    $scope.formInputs[id] = {};
                }
        
                $scope.cancel = function(id){
                    var idx = _getIndexRow(id);
                    if(idx >= 0){
                        delete $scope.source[idx].edit;
                    }else{
                        $scope.addMode = false;
                    }
                    $scope.formInputs[id] = {};
                }
                
                // Get the next id.
                $scope.getNextId = function(type){
                    var ids = $scope.source.map(function(item){
                        return item.id;
                    });
                    return ids.length ? Math.max.apply(null, ids) + 1 : 1;
                }
        
                //Check Variable
                $scope.isValid = function(item){
                    return (item.name && item.name.length) && (item.description && item.description.length);
                }
            }],
            templateUrl: 'templates/data-table.html'
        }
    })
    .controller('addModelCtrl', ['$scope', 'ModelsService', 'TermsService', 'FileUploader', 'SweetAlert', function ($scope, ModelsService, TermsService, FileUploader, SweetAlert) {

        $scope.alerts = [];
        $scope.models = [];
        $scope.uploader = new FileUploader({
            url: '/models/public/api.php/models'
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
            fileItem.id = response.id;
            $scope.models.push({ id: response.id, tags: []});
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
    .controller('searchCtrl', ['$scope', 'ModelsService', 'TermsService', '$log', 'SweetAlert', function ($scope, ModelsService, TermsService, $log, SweetAlert) {

        $scope.itemsPerPage = 8;
        $scope.orderBy = 'desc';
        $scope.totalModels = 0;
        $scope.currentPage = 1;
        $scope.maxSize = 5;
        $scope.models = [];
        $scope.tags = [];

        $scope.getModels = function(){
            var tags = $scope.tags.map(function(tag){ return tag.id}).join(",");
            var start = $scope.itemsPerPage * ($scope.currentPage - 1);
            ModelsService.getModels(start, $scope.itemsPerPage, tags, $scope.orderBy ).then(function(response){
                response.data.forEach(function(model){
                    model.tags.forEach(function(tag){
                        tag.sync = true;
                    });
                });
                $scope.models = response.data;
            });
        }

        $scope.loadTags = function(text){
            return TermsService.getMatchingTerms(text).then(function(response){
                return response.data.terms; 
            });
        }
        
        $scope.changeOrder = function(order){
            $scope.orderBy = order;
            $scope.getModels();
        }

        //Delete model
        $scope.deleteModel = function (idModel) {
               
               var idx = $scope.models.findIndex(function(model){
                   return model.id == idModel;
               });
               
               var model = $scope.models[idx];
               
               SweetAlert.swal({
                   title: "Delete this model?",
                   text: "Are you sure you want to delete "+ model.name +" ?",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",confirmButtonText: "Yes, delete it!",
                   cancelButtonText: "No",
                   closeOnConfirm: false,
                   closeOnCancel: false 
                }, function(isConfirm){ 
                   if (isConfirm) {
                       ModelsService.deleteModel(model.id).then(function(response){
                           $scope.models.splice(idx,1);
                           SweetAlert.swal("Deleted!", model.name + " has been deleted.", "success");
                       });
                   }else{
                       SweetAlert.swal("Cancelled", model.name + " is safe :)", "error");
                   }
              });
        }
        
        
        ModelsService.getCountModels().then(function (response) {
            $scope.totalModels = response.data;
        })
    }])
    .controller('predictionCtrl', ['$scope', 'TermsService', function($scope, TermsService){

        $scope.model = {
            name: '',
            tags:{
                tag: []
            },
            objetiveFunction: '',
            variables:{
                input: [],
                output: []
            }
        }

        $scope.originalModel = angular.copy($scope.model);
        $scope.inputHeaders = ["Input Variable", "Description", "Actions"];
        $scope.outputHeaders = ["Ouput Variable", "Description", "Actions"];

        //Load Tags
        $scope.loadTags = function(text){
            return TermsService.getMatchingTerms(text).then(function(response){
                return response.data.terms; 
            });
        }

    }])
    .controller('optimizationCtrl', ['$scope', function($scope){
        
    }])
    .controller('tagsCtrl', ['$scope' , 'TermsService', 'ModelsService', 'SweetAlert',  function($scope, TermsService, ModelsService, SweetAlert){
        
        var _getModelById = function(id){
            return $scope.$parent.models && $scope.$parent.models.find(function(model){
                return model.id == id;
            });
        }
        
        $scope.getModelTags = function(id){
            var model = _getModelById(id);
            return model.tags;
        }
        
        //Load Tags
        $scope.loadTags = function(text){
            return TermsService.getMatchingTerms(text).then(function(response){
                return response.data.terms; 
            });
        }

        //Save model tags
        $scope.saveTags = function (id) {
            var model = _getModelById(id);
            if (model) {
                var tagsToSync = model.tags.filter(function(tag){ return !tag.sync});
                //Save Model Tags
                ModelsService.saveTags(model.id, tagsToSync).then(function (response) {
                    var ids = response.data.ids;
                    for(var i = 0, len = ids.length; i < len; i++){
                        tagsToSync[i].id = ids[i];
                        tagsToSync[i].sync = true;
                     }
                     SweetAlert.swal("Saved!", "tags were saved succesfully", "success");
                });
            }
        }

        //has tags
        $scope.hasTags = function(id){
            var model = _getModelById(id);
            return model && model.tags.length;
        }

        //has no sync tags
        $scope.hasNotSyncTags = function(id){
            var model = _getModelById(id);
            var hasSyncTags = false;
            if(model){
                hasSyncTags = model.tags.filter(function(tag){
                    return !tag.sync;
                }).length;
            }
            return hasSyncTags;
        }

        //Drop Model Tags
        $scope.dropTags = function (id) {
            var model = _getModelById(id);
            if (model) {
                var tagsToDelete = model.tags.filter(function(tag){
                    return tag.sync;
                }).map(function(tag){
                    return tag.id;
                }).join(',');
                
                if(tagsToDelete.length){
                    ModelsService.deleteTags(model.id, tagsToDelete).then(function(response){
                        SweetAlert.swal("Saved!", "tags were droped succesfully", "success");
                        model.tags.splice(0, model.tags.length);
                    });
                }else{
                    model.tags.splice(0, model.tags.length);
                } 
            }
        }
        
        
    }])
    .controller('saveModelCtrl', ['$scope', 'X2JS', 'SweetAlert', function($scope, X2JS, SweetAlert){
        //Reset Model
        $scope.reset = function(){
            $scope.$parent.model = $scope.$parent.originalModel;
        }
        //Save Model
        $scope.saveModel = function(){
            var variables = $scope.$parent.model.variables;
            if(variables.input.length && variables.output.length){
                var content = X2JS.json2xml_str( $scope.$parent.model );
                var xmlAsStr = "<?xml version='1.0' encoding='UTF-8' ?><model>"+content+"</model>";
                console.log("El XML resultante : " , xmlAsStr);
            }else{
                SweetAlert.swal("Here's a message!", "It's pretty, isn't it?");
            }
        }

    }]);
