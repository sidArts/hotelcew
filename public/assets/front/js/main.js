




$(document).ready(function() {

  /*$("body").click(function(event) {
    $(".me").hide(600);
    event.stopPropagation();
  });*/


    


  $("#list-view").click(function(event) {
    $(".listItem").addClass("col-lg-12 aprtGrd");
    $(".listItem").removeClass("col-lg-4 col-md-4 col-sm-6 col-xs-6");
    event.stopPropagation();
  });

  $("#grid-view").click(function(event) {
    $(".listItem").removeClass("col-lg-12 aprtGrd");
    $(".listItem").addClass("col-lg-4 col-md-4 col-sm-6 col-xs-6");
    event.stopPropagation();
  });


    // Code for Main Banner
    var owl = $('#owl-banner');
    owl.owlCarousel({
      margin: 0,
      nav: true,
      loop: true,
      items:1,
      dots:false,
      //animateOut: 'fadeOut',
      autoplay:true,
      autoplayTimeout:3000,
      autoplayHoverPause:true,
      navText: [
      '<span aria-label="' + 'Previous' + '">&#x2039;</span>',
      '<span aria-label="' + 'Next' + '">&#x203a;</span>'
       ]
    });

    $('.propProdSlid').owlCarousel({
    loop:true,
    margin:15,
    nav:true,
    dots:false,
    responsive:
      {
          0:{
              items:2
          },
          600:{
              items:3
          },
          1000:{
              items:4
          }
      }
    });

    $('#recentlyViewSlid').owlCarousel({
    loop:true,
    margin:30,
    nav:true,
    dots:false,
    responsive:
      {
          0:{
              items:2
          },
          600:{
              items:3
          },
          1000:{
              items:4
          }
      }
    });

    $('#itemDtls').owlCarousel({
    loop:true,
    margin:30,
    nav:true,
    dots:false,
    animateOut: 'slideOutUp',
    animateIn: 'slideInUp',
    responsive:
      {
          0:{
              items:2
          },
          600:{
              items:3
          },
          1000:{
              items:2
          }
      }
    });

    $('.nav-tabs a').on('click', function (event) {
        event.preventDefault();
        
        $('.active').removeClass('active');
        $(this).parent().addClass('active');
        $('.tab-pane').hide();
        $($(this).attr('href')).show();
    });

    $('.nav-tabs a:first').trigger('click');


    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
      " - $" + $( "#slider-range" ).slider( "values", 1 ) );


    $('.smImg > img').click(function(){
      //$(this).addClass('active')
      m = ($(this).attr('src'));
      //console.log(m);
      $('.bigImg > img').attr('src', m);

    });

    $('#ex1').zoom();
  
     $('.flexslider').flexslider({
          animation: "slide",         
          direction: "vertical",
          animationLoop: false,
          slideshow: false
          // minItems: getGridSize(), // use function to pull in initial value
          // maxItems: getGridSize(), // use function to pull in initial value
          
        });

});


// var $window = $(window),
//           flexslider = { vars:{} };

//       // tiny helper function to add breakpoints
//       // function getGridSize() {
//       //   return (window.innerWidth < 600) ? 2 :
//       //          (window.innerWidth < 900) ? 3 : 4;
//       // }

    

//       $window.load(function() {
       
//       });