$(document).ready(function(){
  var timer = null;
  var period = 1000;
  
  var updateCount = function() {
    $.get('/api/getcellcount', function(res){
        $('#counter').text(res.count);
        timer = setTimeout(updateCount, period);
    });
  };

  setTimeout(updateCount, period);
});
