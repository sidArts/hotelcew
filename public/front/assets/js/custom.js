

// Owl 

  jQuery(document).ready(function($) {

    $('.home-carousel').owlCarousel({

      items: 1,      

      autoplay: true,

      nav: false,

      dots:true,

      loop: true

    });



    $('.room-carousel').owlCarousel({

      nav:true,

      dots:false,

      margin:30,

      responsiveClass:true,

      responsive:{

          0:{

              items:1

          },

          768:{

              items:2

          },

          1000:{

              items:3

          }

      }

    });

 

  });







// LightBox



jQuery(document).on('click', '[data-toggle="lightbox"]', function(event) {



    event.preventDefault();



    $(this).ekkoLightbox();



});





// Scroll Fixed



// $(window).scroll(function(){

//     if ($(window).scrollTop() >= 100) {

//         $('.bd_fix').addClass('fixed-top');

//     }

//     else {

//         $('.bd_fix').removeClass('fixed-top');

//     }

// });







// Menu



$(document).ready(function(){

  $("#toggle1").click(function(){

    $("#top_menu").toggle();

  });

});



$(document).ready(function(){
$(".numbersOnly").keydown(function (e) {
   // Allow: backspace, delete, tab, escape, enter and .
   if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
   // Allow: Ctrl+A, Command+A
   (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
   // Allow: home, end, left, right, down, up
   (e.keyCode >= 35 && e.keyCode <= 40)) {
       // let it happen, don't do anything
       return;
   }
   // Ensure that it is a number and stop the keypress
   if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
       e.preventDefault();
   }
});

});










