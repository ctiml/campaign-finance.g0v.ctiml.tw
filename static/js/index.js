$(document).ready(function(){
  var timer = null;
  var period = 10000;
  
  var updateCount = function() {
    $.get('/api/getcellcount', function(res){
        $('#counter').text(res.count);
        $('#todo').text(res.todo);
        timer = setTimeout(updateCount, period);
    });
  };

  updateCount();
});
