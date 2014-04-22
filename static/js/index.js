$(document).ready(function(){

  // @lackneets
  (function refreshCounter(){
  	function getTotalCount(callback){ var s = this; return $.getJSON('/api/getcellcount?callback=?', function(response){callback && callback.call(s, response.count); }); }
  	var interval = 4000;
  	var current = 0;
  	var history = 0;
  	var increment = 0;
  	var timer;
  	(function renew(){
  		getTotalCount(function(count){
  			count = parseInt(count);
  			clearInterval(timer);
  			if(history){
  				increment = count-history; 
  				timer = setInterval(function(){
  					current += increment/(interval/80);
  					$('#counter').text(Math.ceil(current));
  				}, 100);
  			}

  			if(count >= current) {
  				$('#counter').text(count);
  			}
  			history = count;
  			current = count;
  			setTimeout(renew, interval);
  		});
  	})();	
  })(); //End refreshCounter


});
