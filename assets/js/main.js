$(function() {

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


  $('.ui.main.image').cropper();

  $('.left90').click(function() {
    $('.ui.main.image').cropper('rotate', -90);
  });

  $('.right90').click(function() {
    $('.ui.main.image').cropper('rotate', 90);
  });

  $('.left10').click(function() {
    $('.ui.main.image').cropper('rotate', -10);
  });

  $('.right10').click(function() {
    $('.ui.main.image').cropper('rotate', 10);
  });

  $('.zoomin').click(function() {
    $('.ui.main.image').cropper('zoom', 0.1);
  });

  $('.zoomout').click(function() {
    $('.ui.main.image').cropper('zoom', -0.1);
  });

  $('.save').click(function() {
    var img = $('.ui.main.image').cropper('getDataURL', 'image/jpeg');

    $.ajax({
      type: 'POST',
      url: 'save.php',
      data: {
          idni: $.QueryString['idni'],
          img: img
      }
    }, function(data) { console.log('hola'); window.location.href = 'index.php'; });
  });
});
