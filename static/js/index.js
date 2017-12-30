$(document).ready(function(){
  var timer = null;
  var period = 10000;
  
  var updateCount = function() {
    $.get('/api/getcellcount', function(res){
        if (res.round > 0) {
            $('#round-1').hide();
            $('#round-2').show();
            $('#round-finish-2').text(res.round);
            $('#round-todo-2').text(res.round + 1);
            $('#counter-2').text(res.count);
            $('#todo-2').text(res.todo);
        } else {
            $('#counter').text(res.count);
            $('#todo').text(res.todo);
        }
        timer = setTimeout(updateCount, period);
    });
  };

  updateCount();
});
