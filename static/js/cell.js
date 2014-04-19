$(document).ready(function(){

  var submitAnswer = function(e) {
    if (e !== undefined) {
      e.preventDefault();
    }

    var ans = $('#ans').val();
    if (ans === "" && $('#no-content:checked').size() === 0 ) {
      return;
    }
    var page = $('.cell-info').data('page');
    var x = $('.cell-info').data('x');
    var y = $('.cell-info').data('y');

    $('#submit').attr('disabled', 'disabled');

    var url = ['/api/fillcell/', page, "/", x, "/", y].join("");
    $.post(url, { ans: ans }, function(res){
      getRandomImage();
      $('#submit').removeAttr('disabled');
    });
  };

  var getRandomImage = function() {
    $('#ans').val("").focus();
    $('#no-content:checked').prop('checked', false);
    $('.cell-info').text("圖片載入中...");
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
      }

    });
  };
  getRandomImage();

  $('#submit').click(submitAnswer);

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
