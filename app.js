fillController.$inject = ['$scope'];
function fillController($scope) {
    $scope.fname = '';
    $scope.lname = '';
    $scope.city = '';
    $scope.bday = '';
    var autoClar = true;
    var autoLocation = true;

    // The form fill feature works in the way it updates the secondary
    // field using primary fields if the secondary field is not
    // modified. If secondary field is modified to match the
    // calculated value, it turns the auto-update back on.
    
    $scope.updateClar = function() {
	if (autoClar) $scope.clar = defaultClar();
    };

    $scope.autoClarCheck = function() {
	autoClar = $scope.clar === defaultClar();
    }

    $scope.updateLocation = function() {
	if (autoLocation) $scope.location = defaultLocation();
    };

    $scope.autoLocationCheck = function() {
	autoLocation = $scope.location === defaultLocation();
    }

    function defaultClar() {
	return $scope.fname.split(' ', 1)[0] + ' ' + $scope.lname;
    }

    function defaultLocation() {
	return $scope.city;
    }
}
angular.module('FillApp', []).controller('FillController', fillController);
