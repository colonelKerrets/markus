
$(window).scroll(function() {
// 100 = The point you would like to fade the nav in.
  
	if ($(window).scrollTop() > 0 ){
    console.log($(window).scrollTop());
 		$('.bg').addClass('show');
 		$('nav li a').addClass('navshow');
 		$('.logo').removeClass('logoWhite');
 		

 		
 		
    
  } else {
	  console.log($(window).scrollTop());
    
    $('.bg').removeClass('show');
    $('nav li a').removeClass('navshow');
    $('.logo').addClass('logoWhite');
   
    
 	};   	
});

