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

    // trim 掉前後空白
    ans = jQuery.trim(ans);
    var url = ['/api/fillcell/', page, "/", x, "/", y].join("");
    $.post(url, { ans: ans, sToken: $('[name="sToken"]').val() }, function(res){
      // 射後不理(?)
    });
    getRandomImage();
    $('#submit,#no-content').removeAttr('disabled');

    // 將回答過的答案存起來
    if (ans.length > 0 && submitted_answers.indexOf(ans) === -1) {
      submitted_answers.push(ans);
    }
    $('#ans-shadow').val("");
  };

  // 記錄回答過的答案
  var submitted_answers = [];

  if (localStorage) {
    if (localStorage.submitted_answers && Array.isArray(JSON.parse(localStorage.submitted_answers))) {
      submitted_answers = JSON.parse(localStorage.submitted_answers);
    }
    setInterval(function() {
      localStorage.submitted_answers = JSON.stringify(submitted_answers);
    }, 60000);
  }

  var set_question = function(res){
      if (typeof(res) === 'undefined') { return; }
      var img = $('<img></img>').attr('src', res.img_url).bind('error', function(){ getRandomImage(); });
      img.bind('load', function() {
        $('.cell-image').html(img);
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
          //$('.confirm').show();
          $('.confirm').removeClass('disabled');
        }
        $('#unclear').show();
      });
  };

  var question_pools = [];

  var getRandomImage = function() {
    $('#ans').val("").focus();
    $('.cell-info').text("圖片載入中...");
    //$('.confirm').hide();
    $('.confirm').addClass('disabled');
    $('.cell-image').html("");
    $('#unclear').hide();

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
  $('#unclear').click(function() {
    var page = $('.cell-info').data('page');
    var x = $('.cell-info').data('x');
    var y = $('.cell-info').data('y');
    var url = ['/api/reportunclear/', page, "/", x, "/", y].join("");
    $.post(url, { sToken: $('[name="sToken"]').val() }, function(res){});
    getRandomImage();
  });

  $('#next').click(getRandomImage);

  $('#ans').keypress(function(e) {
    if (e.which == 13) {
      if (e.shiftKey) {
        submitAnswer.apply($("#no-content")[0]);
      } else if (e.ctrlKey) {
        submitAnswer.apply($("#confirm")[0]);
      } else {
        submitAnswer();
      }
      e.preventDefault();
    }
  });

  var candidates = [];
  var candidate_index = 0;
  var search_candidates = function(ans, collection) {
    return collection.filter(function(a) {
      var escaped_ans = ans.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
      return a.match("^" + escaped_ans + ".+");
    }).sort();
  }
  var find_candidate_index = function(ans_shadow) {
    var index = candidates.indexOf(ans_shadow);
    return (index < 0) ? 0 : index;
  }

  // 按 tab 鍵補完
  var ans_ac_keydown = function(e) {
    var ans_shadow = $('#ans-shadow').val();
    var ans = $('#ans').val();
    if (ans_shadow !== "" && ans_shadow != ans ) {
        if (e.which == 39 && this.selectionStart == ans.length && ans == ans_shadow.substr(0,ans.length)) {
            $('#ans').val(ans_shadow.substr(0,ans.length+1));
            // 重新篩選自動完成答案
            candidates = search_candidates($('#ans').val(), submitted_answers);
            candidate_index = find_candidate_index($('#ans-shadow').val());
            e.preventDefault();
        } else if (e.which == 9) {
            $('#ans').val(ans_shadow);
            e.preventDefault();
        } else if (e.which == 40) { // Arrow Down
          if (candidates[candidate_index + 1] !== undefined) {
            $('#ans-shadow').val(candidates[++candidate_index]);
          }
          e.preventDefault();
        } else if (e.which == 38) { // Arrow Up
          if (candidates[candidate_index - 1] !== undefined) {
            $('#ans-shadow').val(candidates[--candidate_index]);
          }
          e.preventDefault();
        }
    }
  }

  // 找出自動完成
  var ans_ac_input = function(e) {
    var ans = $('#ans').val();
    if (ans === "") {
      $('#ans-shadow').val("");
      return;
    }
    candidates = search_candidates(ans, submitted_answers);
    candidate_index = find_candidate_index($('#ans-shadow').val());
    $('#ans-shadow').val((candidates.length > 0) ? candidates[candidate_index] : "");
  };

  $('#autocomplete-trigger').change(function() {
    if ($('#autocomplete-trigger').is(":checked")) {
      $('#ans').bind('input', ans_ac_input).bind('keydown', ans_ac_keydown);
      $('#ans-shadow').removeClass("hidden");
    } else {
      $('#ans').unbind('input', ans_ac_input).unbind('keydown', ans_ac_keydown);
      $('#ans-shadow').addClass("hidden");
    }
  });

  var triggerButton = function ( buttonId, e ) {
        $( buttonId ).trigger('click');
	if (e) e.preventDefault();
  };
  $('#ans').keydown(function(e) {
    switch(e.which){
      case 40:	//down
        triggerButton( '#confirm', e );
	break;
      case 32:	//space
        triggerButton( '#no-content', e );
	break;
    }
  });

});
