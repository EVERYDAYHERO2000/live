var formdata = false,
  storage = localStorage,
  currentpost;

if (window.FormData) {
  formdata = new FormData();
}



$(function () {

  var prevPath;

  if (!storage.login) {
    storage.setItem('login', prompt('логин на yandex-team.ru', '@'));
  }

  if (storage.liveboard) {
    data = JSON.parse(storage.liveboard);
    buildPage(data);
  }

  var updateAnimation = new animation($('.status'), 'statusSpinner', 'Обновляю', 'float:right')
  $.get("loadfolderslist.php", function (data) {
    storage.setItem('liveboard', data);
    data = JSON.parse(data);
    buildPage(data);
    updateAnimation.stop();
    console.log('Disk data loaded')
  });

  function buildPage(data) {

    $('.albom').html('');
    for (var i = 0; i < data.length; i++) {
      if (data[i]._embedded) {
        for (var p = 0; p < data[i]._embedded.items.length; p++) {
          var tempPath = data[i]._embedded.items[p].path.replace(data[i]._embedded.items[p].name, '');

          if (prevPath !== tempPath) {

            if (data[i]._embedded.items[p].media_type === 'image') {
              prevPath = tempPath;
              $('.albom').prepend('<div class="preview" data-prv="' + data[i]._embedded.items[p].preview + '" data-path="' + data[i]._embedded.items[p].path + '" style="background-image:url(' + data[i]._embedded.items[p].preview + ')"></div>');
              break;
            }
          }
        }
      }
    }
  }


  function addNewPost(data, num){
    num = num || new Number( $('.pageData__post:last-of-type').attr('data-post') ) + 1;
    
    $('.pageData').append('<div class="pageData__post" data-post="' + num + '" ></div>');
    $('.pageData__post[data-post=' + num + ']').append('<div><a target="_blank" href="https://staff.yandex-team.ru/' + data.user + '">' + data.user + '</a> >> ' + data.date.replace(/([0-9\-]{10})_([0-9]{2})([0-9]{2})([0-9]{2})/, '$1 $2:$3') + '</div>');

    if (data.comment !== '') {
      $('.pageData__post[data-post=' + num + ']').append('<p>' + data.comment + '</p>');
    }

    if (data.href) {
      $('.pageData__post[data-post=' + num + ']').append('<img class="pageData__image" src="' + data.href + '"/>');
    }

    $('.pageData__post[data-post=' + num + ']').append('<p>------------------</p>');
    
    return $('.pageData__post[data-post=' + num + ']');
    
  }
  

  $('body').on('click', function (e) {
    if ($(e.target).is('.close')) {
      $('.overlay').remove();
      $('body').removeClass('scrollDisable');
    }
    if ($(e.target).is('.preview')) {
      var path = $(e.target).attr('data-path');
      currentpost = path;
      var prvImage = $(e.target).attr('data-prv');
      $('body').append('<div class="overlay"><div class="close">[x]</div><div class="pageData"></div><form class="folder-form"><label>Комментарий</label><textarea class="comments" autofocus></textarea><input class="files" id="postfile" type="file" name="images" /><div id="sendpost" class="button">Добавить</div><div class="postStatus"></div></form></div>');

      var loadingPageData = new animation($('.pageData'), 'pagedataSpinner', 'Открываю папку на диске');

      $('body').addClass('scrollDisable');
      $.get("openfolder.php", {
        path: path
      }, function (data) {

        loadingPageData.stop();

        data = JSON.parse(data);

        for (var i = 0; i < data.length; i++) {
          addNewPost(data[i], i + 1);
        }
      });



    }

    if ($(e.target).is('#sendpost') && !$(e.target).is('.button_disabled')) {

      var loadedImage;
      var newpostAnimation = new animation($('.postStatus'), 'newPostSpinner', 'Добавляю комментарий')
      $('#sendpost').addClass('button_disabled');

      var file = $('#postfile')[0].files[0];
      if (file) {
        if (window.FileReader) {
          reader = new FileReader();
          reader.onloadend = function (e) {
            loadedImage = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      }

      if (formdata) {
        formdata.delete('images[]');
        formdata.delete('comment');
        if (file) {
          formdata.append('images[]', file);
        }
        formdata.append('login', storage.login);
        formdata.append('comment', $('.comments').val());
        formdata.append('path', currentpost);

        $.ajax({
          url: "newpost.php",
          type: "POST",
          data: formdata,
          processData: false,
          contentType: false,
          success: function (res) {

            var target = addNewPost({
              date : 'Сейчас',
              user : storage.login,
              href : loadedImage,
              comment: $('.comments').val()
            });
            
            $('.overlay').scrollTo(target, { duration : 500 });
            
            $('#postfile').val('');
            $('.comments').val('');
            newpostAnimation.stop();
            $('#sendpost').removeClass('button_disabled');

          }
        });


      }
    }

  });


  function animation(elem, id, text, style) {

    var anim = ['| ', '/ ', '— ', '\\ '];

    var _this_ = this;
    _this_.speed = 200;
    _this_.elem = elem;
    _this_.step = 0;
    _this_.text = text;

    _this_.elem.attr('style', style);
    _this_.loading = setInterval(function () {
      if (_this_.step < 3) {
        _this_.step++;
      } else {
        _this_.step = 0;
      }
      elem.append('<div class="spinner" id="' + id + '"></div>')
      $('#' + id).html(_this_.text + ' ' + anim[_this_.step]);
    }, _this_.speed);

    _this_.stop = function () {
      clearInterval(_this_.loading);
      _this_.step = 0;
      _this_.elem.attr('style', '');
      _this_.elem.html('');
    }

  }


  function showUploadedItem(source, res) {
    var res = res || '';
    var albom = $('.albom').prepend('<div class="preview" data-prv="' + source + '" data-path="' + res + '" style="background-image:url(' + source + ')"></div>');
  }

  $('#images').change(function (e) {
    var len = $(this)[0].files.length,
      img, reader, file, loadedImage;

    var loadingAnimation = new animation($('.status'), 'uploadSpinner', 'Загрузка');

    for (var i = 0; i < len; i++) {
      file = $(this)[0].files[i];

      if (!!file.type.match(/image.*/)) {

        if (window.FileReader) {
          reader = new FileReader();
          reader.onloadend = function (e) {
            loadedImage = e.target.result;
            //showUploadedItem(e.target.result);
          };
          reader.readAsDataURL(file);
        }

        if (formdata) {
          formdata.delete('images[]');
          formdata.append('images[]', file);
          formdata.append('login', storage.login);

          $.ajax({
            url: "newfolder.php",
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (res) {

              showUploadedItem(loadedImage, res);
              loadingAnimation.stop();

              $('#images').val('');

            }
          });

        }

      }
    }


  })
});