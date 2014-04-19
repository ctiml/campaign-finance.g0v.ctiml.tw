$(document).ready(function(){

  var submitAnswer = function(e) {
    if (e !== undefined) {
      e.preventDefault();
    }

    var ans = $('#ans').val();

    if ($(this).hasClass("confirm")) {
      ans = $('.cell-info').data('ans');
    }

    if (ans === "" && $(this).hasClass("no-content") === false) {
      return;
    }

    var page = $('.cell-info').data('page');
    var x = $('.cell-info').data('x');
    var y = $('.cell-info').data('y');

    $('#submit,#no-content').attr('disabled', 'disabled');

    var url = ['/api/fillcell/', page, "/", x, "/", y].join("");
    $.post(url, { ans: ans }, function(res){
      getRandomImage();
      $('#submit,#no-content').removeAttr('disabled');
    });
  };

  var getRandomImage = function() {
    $('#ans').val("").focus();
    $('.cell-info').text("圖片載入中...");
    $('.confirm').hide();
    $('.cell-image').html("");
    
    $.get('/api/getrandom', function(res) {
      $('.cell-image').html($('<img></img>').attr('src', res.img_url).bind('error', function(){ getRandomImage(); }));
      $('.cell-info').data({
        page: res.page,
        x: res.x,
        y: res.y,
        ans: res.ans
      })
      .text("")
      .append($('<span></span>').text("第 "+res.page+" 頁 ("+res.x+", "+res.y+" )"));

      if (res.ans !== "") {
        $('.cell-info').append($('<span></span>').text(" 已經有人填寫了：" + res.ans));
        $('.confirm').show();
      }

    });
  };
  getRandomImage();

  $('#submit').click(submitAnswer);
  $('#no-content').click(submitAnswer);
  $('#confirm').click(submitAnswer);

  $('#next').click(getRandomImage);

  $('#ans').keypress(function(e) {
    if (e.which == 13) {
      if (e.shiftKey) {
        getRandomImage();
      }
      e.preventDefault();
      submitAnswer();
    }
  });

});
