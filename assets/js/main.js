$(function() {
      var idni;
      var page;
      $('a.page').click(function() {
	 idni = $(this).attr('name');
	 page = $(this).attr('page');
         $('.modal.page').modal('show');
         return false;
      });


        $('.ui.button.set-pagina').on('click', function() {
			    var pagina = 0;
			    if ($(this).html() > 0) {
				pagina = $(this).html()
			    }
			    $.ajax({
			      type: 'POST',
			      url: 'save.php',
			      data: {
				  idni: idni,
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
			
			    $.ajax({
			      type: 'POST',
			      url: 'save.php',
			      data: {
				  idni: $.QueryString['idni'],
          			  ndni: $("#ndni").val(),
				  cara: 0
			      }
			    });
			
		} else {
        		$('.ui.button.cara[name="frontal"]').removeClass("active");

			    $.ajax({
			      type: 'POST',
			      url: 'save.php',
			      data: {
				  idni: $.QueryString['idni'],
          			  ndni: $("#ndni").val(),
				  cara: 1
			      }
			    });
		}
	});

	function check_dni(dni)
	{
	    // Comprobamos si tiene longitud 9
	    if(dni.length == 9)
	    {
		// Extraemos los 8 primeros caracteres
		numbers_DNI = dni.substring(0,8);
	 
		// Función que comprueba si un número es un
		// entero no negativo
		var isInteger = function(n)
		{
		    var intRegex = /^\d+$/;
		    if(intRegex.test(n)) return true;
		    return false;
		}
	 
		// Comprobamos si los 8 primeros caracteres
		// son números
		if(!isInteger(numbers_DNI)) return false;
	 
		// Extraemos el último caracter
		letter_DNI = dni.substring(8);
	 
		// Función que hemos elaborado antes para
		// el cálculo de la letra
		var get_letter_DNI = function(dni)
		{
		    var table = "TRWAGMYFPDXBNJZSQVHLCKE";
		    return table.charAt(dni % 23);
		}
	 
		// Calculamos la letra de las cifras que se
		// han introducido
		letter_calculated = get_letter_DNI(numbers_DNI);
	 
		// Si la letra es correcta damos por válido el DNI
		if(letter_calculated == letter_DNI) return true;
	    }
	 
	    return false;
	}
	 
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))


  $('.ui.main.image').cropper({
     preview: '.preview',
     aspectRatio: 16/10,
     autoCropArea: 0.95,
     crop: function(data) {
         $("#dimensions").html("width: " + parseInt(data.width, 10) + ", height: " + parseInt(data.height, 10));
     }
  });

  $('.left90').click(function() {
    $('.ui.main.image').cropper('rotate', -90);
  });

  $('.right90').click(function() {
    $('.ui.main.image').cropper('rotate', 90);
  });

  $('.left5').click(function() {
    $('.ui.main.image').cropper('rotate', -5);
  });

  $('.right5').click(function() {
    $('.ui.main.image').cropper('rotate', 5);
  });

  $('.zoomin').click(function() {
    $('.ui.main.image').cropper('zoom', 0.1);
  });

  $('.zoomout').click(function() {
    $('.ui.main.image').cropper('zoom', -0.1);
  });

  $('.save').click(function() {
      $('.modal.crop').modal('show');
      var img = $('.ui.main.image').cropper('getDataURL', 'image/jpeg');
      $('.preview2').attr('src', img);
      $('.ndni-modal').html($("#ndni").val());
  }); 


  $cara = $('.ui.button.cara[name="reverso"]').hasClass("active") && 1 || 0;

  $('.save-dni').click(function() {
    var dni = $("#ndni").val();
    if (check_dni(dni)) {
	    $.ajax({
	      type: 'POST',
	      url: 'save.php',
	      data: {
		  idni: $.QueryString['idni'],
	          cara: $cara,
		  ndni: $("#ndni").val()
	      },
	      complete: function(data) { 
		$('.save-dni').addClass('positive');
		$('.save-dni').html('Tot correcte');
	//	window.location.href = 'index.php'; 
	      }
	    });
    } else {
        alert("El DNI introduït no es vàlid");
    }
  });

  $('.save2').click(function() {
    var img = $('.ui.main.image').cropper('getDataURL', 'image/jpeg');

    var dni = $("#ndni").val();
    if (!check_dni(dni)) {
        alert("El DNI introduït no es vàlid");
	return;
    }

    $.ajax({
      type: 'POST',
      url: 'save.php',
      data: {
          idni: $.QueryString['idni'],
          ndni: $("#ndni").val(),
	  cara: $cara,
          img: img
      },
      complete: function(data) { 
	window.location.href = 'index.php'; 
      }
    });
  });
});
