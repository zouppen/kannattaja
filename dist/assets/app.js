fillController.$inject = ['$scope', '$location'];
function fillController($scope, $location) {
    $scope.fname = '';
    $scope.lname = '';
    $scope.city = '';
    $scope.bday = '';
    var autoClar = true;
    var autoLocation = true;

    // If there is party name in URL, fill it
    $scope.$on('$locationChangeSuccess', function(event) {
	$scope.party = $location.path().substr(1);
    });
    
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

    $scope.clean = function() {
	// Clean all but location and party
	autoClar = true;
	if ($scope.location === '') autoLocation = false;
	$scope.fname = '';
	$scope.lname = '';
	$scope.clar = '';
	$scope.city = '';
	$scope.bday = '';
	$scope.showPdf = false;
	fname.focus();
    }

    document.getElementById('card').onload = function() {
	// Suppressing the need for Firefox to print about:blank at
	// page load.
	if (!$scope.realSubmit) return;

	// This is a hackish way for trying to print. Firefox with
	// native pdf.js doesn't like it and needs some quirks.
	try {
	    // Trying to print causes browsers not supporting the
	    // print go to catch block.
	    card.print();
	    // We are outside AngularJS, repaint
	    $scope.$apply();
	} catch (e) {
	    // We don't support printing so showing the PDF instead
	    $scope.showPdf = true;
	    // We are outside AngularJS, repaint
	    $scope.$apply();
	}
    };
}

config.$inject = ['$locationProvider'];
function config($locationProvider,$compileProvider) {
    $locationProvider.html5Mode(false);
}

angular.module('FillApp', []).controller('FillController', fillController);
