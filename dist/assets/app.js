fillController.$inject = ['$scope', '$location'];
function fillController($scope, $location) {
    $scope.fname = '';
    $scope.lname = '';
    $scope.city = '';
    $scope.bday = '';
    var autoLocation = true;

    // If there is party name in URL, fill it
    $scope.$on('$locationChangeSuccess', function(event) {
	$scope.party = $location.path().substr(1);
	// Focus straight to name if party is prefilled
	($scope.party === '' ? party : fname).focus();
    });
    
    // The form fill feature works in the way it updates the secondary
    // field using primary fields if the secondary field is not
    // modified. If secondary field is modified to match the
    // calculated value, it turns the auto-update back on.

    $scope.updateLocation = function() {
	if (autoLocation) $scope.location = defaultLocation();
    };

    $scope.autoLocationCheck = function() {
	autoLocation = $scope.location === defaultLocation();
    }

    function defaultLocation() {
	return $scope.city;
    }

    $scope.clean = function() {
	// Clean all but location and party
	if ($scope.location === '') autoLocation = false;
	$scope.fname = '';
	$scope.lname = '';
	$scope.city = '';
	$scope.bday = '';
	$scope.showPdf = false;
	fname.focus();
    }

    document.getElementById('card').onload = function() {
	// Suppressing the need for Firefox to print about:blank at
	// page load.
	if (!$scope.realSubmit) return;

	window.setTimeout(function() {
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
	}, 100);
    };
}

config.$inject = ['$locationProvider'];
function config($locationProvider,$compileProvider) {
    $locationProvider.html5Mode(false);
}

angular.module('FillApp', []).controller('FillController', fillController);
