$(document).ready(function(){

  var submitAnswer = function(e) {
    if (e !== undefined) {
      e.preventDefault();
    }

    var ans = $('#ans').val();

    if ($(this).hasClass("confirm")) {
      ans = $('.cell-info').data('ans');
    }

    if ($(this).hasClass("quick-answer")) {
        ans = $(this).data('answer');
    }

    if (ans === "" && $(this).hasClass("no-content") === false && $(this).hasClass("confirm") === false) {
      return;
    }

    var page = $('.cell-info').data('page');
    var x = $('.cell-info').data('x');
    var y = $('.cell-info').data('y');

    $('#submit,#no-content').attr('disabled', 'disabled');

    var url = ['/api/fillcell/', page, "/", x, "/", y].join("");
    $.post(url, { ans: ans, sToken: $('[name="sToken"]').val() }, function(res){
      // 射後不理(?)
    });
    getRandomImage();
    $('#submit,#no-content').removeAttr('disabled');
  };


  var set_question = function(res){
      $('.cell-image').html($('<img></img>').attr('src', res.img_url).bind('error', function(){ getRandomImage(); }));
      $('.cell-info').data({
        page: res.page,
        x: res.x,
        y: res.y,
        ans: res.ans
      })
      .text("")
      .append($('<span></span>').text("第 "+res.page+" 頁 ("+res.x+", "+res.y+" )"));

      if (res.ans !== null) {
        $('.cell-info').append($('<span></span>').text(" 已經有" +res.count + "人填寫確認了，目前答案：").append($('<code></code>').text(res.ans)));
        $('.confirm').show();
      }
  };

  var question_pools = [];

  var getRandomImage = function() {
    $('#ans').val("").focus();
    $('.cell-info').text("圖片載入中...");
    $('.confirm').hide();
    $('.cell-image').html("");

    if (question_pools.length) {
        set_question(question_pools.shift());
        return;
    }
    
    $.get('/api/getrandoms', function(questions){
        question_pools = questions;
        set_question(question_pools.shift());
    });
  };
  getRandomImage();

  $('#submit').click(submitAnswer);
  $('#no-content').click(submitAnswer);
  $('#confirm').click(submitAnswer);
  $('.quick-answer').click(submitAnswer);
  $('#quick-trigger').click(function(){
    $('.quick-answer').toggle();
    $('.open-close').text($('.quick-answer').is(':visible') ? "關閉" : "開啟");
  });

  $('#next').click(getRandomImage);

  $('#ans').keypress(function(e) {
    if (e.which == 13) {
      if (e.shiftKey) {
        submitAnswer.apply($("#no-content")[0]);
      }
      e.preventDefault();
      submitAnswer();
    }
  });

});
