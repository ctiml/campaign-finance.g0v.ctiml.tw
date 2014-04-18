$(document).ready(function(){
  $('#submit').on('click', function(e){
    e.preventDefault();
    alert('send');
  });  

  $('#reset').on('click', function(e){
    window.location.reload();
  });  

});
