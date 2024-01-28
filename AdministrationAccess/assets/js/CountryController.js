var countryapp = angular.module('countryapp', ['ngResource', 'ngSanitize', 'ngFileSaver']);

countryapp.factory('COUNTRY', ['$resource', function ($resource) {
    return $resource('', {}, {
        info: {
            url: 'countryapi.php',
            method: 'POST'
        },
        powertoggle: {
            url: 'countryapi.php',
            method: 'POST'
        },
        SaveConfig: {
            url: 'countryapi.php',
            method: 'POST'
        }
        ,
        download: {
            url: 'countryapi.php',
            method: 'POST'
        }
        ,
        delete: {
            url: 'countryapi.php',
            method: 'POST'
        },
        stats: {
            url: 'countryapi.php',
            method: 'POST'
        },
        reactivate: {
            url: 'countryapi.php',
            method: 'POST'
        },
        autoreactivate: {
            url: 'countryapi.php',
            method: 'POST'
        },
        autoreactivatestatus: {
            url: 'countryapi.php',
            method: 'POST'
        }
    })
}]);

countryapp.filter('status', function () {
    return function (elem) {
        if (elem.status == '1') {
            if (elem.start == elem.stop)
                return 'enable tell Infinity';
            else
                return 'enable tell ' + elem.stop;

        }
        else return 'disabled';
    }
});
countryapp.controller('CountryController', ['$scope', 'COUNTRY', 'FileSaver', '$document', '$filter', function ($scope, COUNTRY, FileSaver, $document, $filter) {
    $scope.countries = {};
    $scope.form = {};
    $scope.useract = 1;



    $scope.getdata = function () {
        COUNTRY.info({ action: 'getdata' }, function (data) {
            $scope.countries = data.info;
            setTimeout(function () {
                $('.timepicker').timepicker();
            }, 10000);
        }, function (err) {
            toastr.error('Faild To Get Data.')
        })
    }
    $scope.powertoggle = function (countryobj) {
        COUNTRY.powertoggle({ action: 'powertoggle', data: countryobj }, function (data) {
            if (data.status == 'ok') {
                let index = $scope.countries.findIndex(x => x.country === countryobj.country);

                if ($scope.countries[index].status == "1")
                    $scope.countries[index].status = "0";
                else
                    $scope.countries[index].status = "1";
                toastr.success('Power switched Successfully')
            }
            else
                toastr.error('Faild To Switch Power.')
        }, function (err) {
            toastr.error('Faild To Switch Power.')
        })
    }

    $scope.SaveConfig = function (countryobj) {
        COUNTRY.SaveConfig({ action: 'SaveConfig', data: countryobj }, function (data) {
            if (data.status == 'ok') {
                toastr.success('Configuration Saved');
                $scope.useract = 1;
            } else {
                toastr.success('Faild To Save')
            }
        }, function (err) {
            toastr.error('Faild To Save')
        })
    }

    $scope.download = function (countryobj) {
        console.log(countryobj)
        COUNTRY.download({ action: 'download', data: countryobj }, function (data) {
            if (data.status == 'ok') {
                var data = new Blob([data.msg], { type: 'text/plain;charset=utf-8' });
                FileSaver.saveAs(data, countryobj.country + '.txt');
            } else {
                toastr.success('Faild To Download')
            }
        }, function (err) {
            toastr.error('Faild To Download.')
        })
    }

    $scope.datachanged = function () {
        $scope.useract = 0;
        setTimeout(function () {
            $scope.useract = 1;
        }, 60000);
    }

    $scope.delete = function (countryobj) {
        $scope.todelete = countryobj;
        $('#modal-delete').modal();
    }
    $scope.deleteCountry = function (choice) {
        if (choice == 'N') {
            COUNTRY.delete({ action: 'deleteN', data: $scope.todelete }, function (data) {
                if (data.status == 'ok') {
                    toastr.success('Numbers Deleted');
                    $scope.useract = 1;
                } else {
                    toastr.success('Faild To Delete');
                }
            }, function (err) {
                toastr.error('Faild To Delete');
            })
        } else
            if (choice == 'NB') {
                COUNTRY.delete({ action: 'deleteNB', data: $scope.todelete }, function (data) {
                    if (data.status == 'ok') {
                        $scope.getdata();
                        toastr.success('Country Deleted');
                        $scope.useract = 1;
                    } else {
                        toastr.success('Faild To Delete');
                    }
                }, function (err) {
                    toastr.error('Faild To Delete');
                })
            }

    }

    $scope.getstats = function () {
        COUNTRY.stats({ action: 'getstats' }, function (data) {
            $scope.stats = data.info;
            angular.forEach($scope.countries, function (value, key) {
                var filteredArray = $filter('filter')($scope.stats, { "country": value.country, "source": value.source });
                // console.log($scope.countries[key][value]);
                angular.extend($scope.countries[key], filteredArray[0]);
            });

            setTimeout(function () {
                $('.timepicker').timepicker();
            }, 1000);
        }, function (err) {
            toastr.error('Faild To Get Data.')
        })
    }
    $scope.reactivate = function (countryobj) {
        COUNTRY.reactivate({ action: 'reactivate', data: countryobj }, function (data) {
            if (data.status == 'ok') {
                $scope.getdata();
                toastr.success('Numbers reactivated');
                $scope.useract = 1;
            } else {
                toastr.success('Faild To Reactivate')
            }
        }, function (err) {
            toastr.error('Faild To Reactivate')
        })
    }
    $scope.autoreactivate = function () {
        COUNTRY.autoreactivate({ action: 'autoreactivate' }, function (data) {
            console.log(data)

            if (data.status == 'ok') {
                toastr.success(data.msg);
                $scope.useract = 1;
                $('#autoreactivatebutton').toggleClass('btn-danger');
            } else {
                toastr.error('Faild To auto reactivate')
            }
        }, function (err) {
            toastr.error('Faild To auto reactivate')
        })
    }
    setInterval(function () {
        if ($scope.useract > 0) {
            $(".timepicker-popover").remove();
            $scope.getdata();
            $scope.useract = 1;
        }
    }, 30000);

    $scope.autoreactivatestatus = function () {
        COUNTRY.autoreactivatestatus({ action: 'autoreactivatestatus' }, function (data) {
            if (data.status == 'ok') {
                if (data.msg == 'stoped') {
                    $('#autoreactivatebutton').toggleClass('btn-danger');
                }
            }
        }, function (err) {
            toastr.error('Faild To Get Data.')
        })
    }

}]);

