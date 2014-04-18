$(document).ready(function(){
  $('#submit').on('click', function(e){
    e.preventDefault();
    var data = {
      page: $('.cell-info').data('page'),
      x: $('.cell-info').data('x'),
      y: $('.cell-info').data('y'),
      ans: $('#ans').val()
    };
    var url = '/api/fillcell/' + data.page + "/" + data.x + "/" + data.y;
    $.post(url, {ans: data.ans}, function(){
      $('.msg').text('ok');
    });
  });  

  $('#reset').on('click', function(e){
    window.location.reload();
  });  

});
