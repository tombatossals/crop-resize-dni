$(function() {
      var ndni;
      $('.dropdown').dropdown();
      $('a.page').click(function() {
	 ndni = $(this).attr('name');
         $('.modal.page').modal('show');
         return false;
      });


      $('.ui.button.set-pagina').on('click', function() {
	    var pagina = 0;
	    if ($(this).html() > 0) {
		pagina = $(this).html()
	    }
console.log(pagina, ndni);
	    $.ajax({
	      type: 'POST',
	      url: 'save.php',
	      data: {
		  ndni: ndni,
		  pagina: pagina
	      },
	      complete: function() {
		$('.modal.page').modal('hide');
		window.location.reload();
	      }
	    });
        });

        $('.ui.button.cara').on('click', function() {
		$(this).addClass('active');
		if ($(this).attr("name") === "frontal") {
        		$('.ui.button.cara[name="reverso"]').removeClass("active");
		} else {
        		$('.ui.button.cara[name="frontal"]').removeClass("active");
		}
	});

  $('.left90').click(function() {
    $('.ui.main.image').cropper('rotate', -90);
  });

  $('.right90').click(function() {
    $('.ui.main.image').cropper('rotate', 90);
  });

  $('.left5').click(function() {
    $('.ui.main.image').cropper('rotate', -1);
  });

  $('.right5').click(function() {
    $('.ui.main.image').cropper('rotate', 1);
  });

  $('.zoomin').click(function() {
    $('.ui.main.image').cropper('zoom', 0.1);
  });

  $('.zoomout').click(function() {
    $('.ui.main.image').cropper('zoom', -0.1);
  });

});

var app = angular.module('dnisApp', ['angular-spinkit'])
app.directive('capitalize', function() {
   return {
     require: 'ngModel',
     link: function(scope, element, attrs, modelCtrl) {
        var capitalize = function(inputValue) {
           if(inputValue == undefined) inputValue = '';
           var capitalized = inputValue.toUpperCase();
           if(capitalized !== inputValue) {
              modelCtrl.$setViewValue(capitalized);
              modelCtrl.$render();
            }         
            return capitalized;
         }
         modelCtrl.$parsers.push(capitalize);
         capitalize(scope[attrs.ngModel]);  // capitalize initial value
     }
   };
});

app.controller('recercaController', ['$scope', '$http', function($scope, $http) {

	$scope.validDni = true;

	var checkNIF = function(dni){
	    if(dni.length == 9)
	    {
		numbers_DNI = dni.substring(0,8);
		var isInteger = function(n)
		{
		    var intRegex = /^\d+$/;
		    if(intRegex.test(n)) return true;
		    return false;
		}
		if(!isInteger(numbers_DNI)) return false;
		letter_DNI = dni.substring(8);
		var get_letter_DNI = function(dni)
		{
		    var table = "TRWAGMYFPDXBNJZSQVHLCKE";
		    return table.charAt(dni % 23);
		}
		letter_calculated = get_letter_DNI(numbers_DNI);
		if(letter_calculated == letter_DNI) return true;
	    }
	 
	    return false;
	};

	var checkTR = function(tr) {
		console.log(tr);
                if ((tr.length!=10) && (tr.length!=9)) return false;
                if ((tr.charAt(0).toUpperCase() != "X") && (tr.charAt(0).toUpperCase() != "Y") && (tr.charAt(0).toUpperCase() != "Z")) return false;
                
                var leftNum = '0';
                if (tr.charAt(0).toUpperCase() == "Y") leftNum = '1';
                
                if (tr.length==9) {
                        return checkNIF(leftNum + tr.substring(1,tr.length));
                } else {
                        return checkNIF(tr.substring(1,tr.length));
                }
	}

	
	$scope.validateDni = function(dni) {
		return checkNIF(dni) || checkTR(dni);
	};
}]);

app.controller('assignarController', ['$scope', '$http', function($scope, $http) {

	$scope.validDni1 = true;
	$scope.validDni2 = true;
	$scope.validDni3 = true;
	$scope.validDni4 = true;
	$scope.validDni5 = true;

	var checkNIF = function(dni){
	    if(dni.length == 9)
	    {
		numbers_DNI = dni.substring(0,8);
		var isInteger = function(n)
		{
		    var intRegex = /^\d+$/;
		    if(intRegex.test(n)) return true;
		    return false;
		}
		if(!isInteger(numbers_DNI)) return false;
		letter_DNI = dni.substring(8);
		var get_letter_DNI = function(dni)
		{
		    var table = "TRWAGMYFPDXBNJZSQVHLCKE";
		    return table.charAt(dni % 23);
		}
		letter_calculated = get_letter_DNI(numbers_DNI);
		if(letter_calculated == letter_DNI) return true;
	    }
	 
	    return false;
	};

	var checkTR = function(tr) {
		console.log(tr);
                if ((tr.length!=10) && (tr.length!=9)) return false;
                if ((tr.charAt(0).toUpperCase() != "X") && (tr.charAt(0).toUpperCase() != "Y") && (tr.charAt(0).toUpperCase() != "Z")) return false;
                
                var leftNum = '0';
                if (tr.charAt(0).toUpperCase() == "Y") leftNum = '1';
                
                if (tr.length==9) {
                        return checkNIF(leftNum + tr.substring(1,tr.length));
                } else {
                        return checkNIF(tr.substring(1,tr.length));
                }
	}

	
	$scope.validateDni = function(dni) {
		return checkNIF(dni) || checkTR(dni);
	};
}]);

app.controller('cropController', ['$scope', '$http', function($scope, $http) {

    $scope.stepTwo = function() {
	if (!$scope.validateDni($scope.ndni)) {
	    return;
        }

        $('.ui.main.image').cropper({
            preview: '.preview',
            aspectRatio: 16/10,
            autoCropArea: 0.99,
            crop: function(data) {
                $("#dimensions").html("width: " + parseInt(data.width, 10) + ", height: " + parseInt(data.height, 10));
            }
        });

	$scope.step = 2;
        return true;
    };

    $scope.stepThree = function() {
	if (!$scope.validateDni($scope.ndni)) {
	    return;
        }
        var img = $('.ui.main.image').cropper('getDataURL', 'image/jpeg');
        if (!img) {
	    step = 1;
            return;
	}
        $('.preview2').attr('src', img);
	$scope.step = 3;
    };

    var parseLocation = function(location) {
      var pairs = location.substring(1).split("&");
      var obj = {};
      var pair;
      var i;

      for ( i in pairs ) {
        if ( pairs[i] === "" ) continue;
        pair = pairs[i].split("=");
        obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
      }

      return obj;
    };

	var checkNIF = function(dni){
	    if(dni.length == 9)
	    {
		numbers_DNI = dni.substring(0,8);
		var isInteger = function(n)
		{
		    var intRegex = /^\d+$/;
		    if(intRegex.test(n)) return true;
		    return false;
		}
		if(!isInteger(numbers_DNI)) return false;
		letter_DNI = dni.substring(8);
		var get_letter_DNI = function(dni)
		{
		    var table = "TRWAGMYFPDXBNJZSQVHLCKE";
		    return table.charAt(dni % 23);
		}
		letter_calculated = get_letter_DNI(numbers_DNI);
		if(letter_calculated == letter_DNI) return true;
	    }
	 
	    return false;
	};

	var checkTR = function(tr) {
		console.log(tr);
                if ((tr.length!=10) && (tr.length!=9)) return false;
                if ((tr.charAt(0).toUpperCase() != "X") && (tr.charAt(0).toUpperCase() != "Y") && (tr.charAt(0).toUpperCase() != "Z")) return false;
                
                var leftNum = '0';
                if (tr.charAt(0).toUpperCase() == "Y") leftNum = '1';
                
                if (tr.length==9) {
                        return checkNIF(leftNum + tr.substring(1,tr.length));
                } else {
                        return checkNIF(tr.substring(1,tr.length));
                }
	}

	
	$scope.validateDni = function(dni) {
		return checkNIF(dni) || checkTR(dni);
	};

	 
    $scope.save = function() {

        $('.modal.server').modal('show');
        var img = $('.ui.main.image').cropper('getDataURL', 'image/jpeg');
        var cara = $('.ui.button.cara[name="reverso"]').hasClass("active") && 1 || 0;
        var idni = parseLocation(window.location.search)['idni'];

        $http({
          method: 'POST',
          url: 'save.php',
          data: { 
              idni: idni,
              ndni: $scope.ndni,
              cara: cara,
              img: img
          }
        }).success(function() {
          window.location.href = 'index.php';
        });

    };
}]);
