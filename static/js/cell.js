$(document).ready(function(){

  var submitAnswer = function(e) {
    if (e !== undefined) {
      e.preventDefault();
    }

    var ans = $('#ans').val();
    if (ans === "") {
      return;
    }
    var page = $('.cell-info').data('page');
    var x = $('.cell-info').data('x');
    var y = $('.cell-info').data('y');

    $('#submit').attr('disabled', 'disabled');

    var url = ['/api/fillcell/', page, "/", x, "/", y].join("");
    $.post(url, { ans: ans }, function(res){
      window.location.reload();
    });
  };

  $('#submit').click(submitAnswer);

  $('#next').click(function(e) {
    window.location.reload();
  });

  $('#ans').keypress(function(e) {
    if (e.which == 13) {
      e.preventDefault();
      submitAnswer();
    }
  }).focus();

});
