/*-----------------------------------------------------------------------------------
/*
/* Init JS
/*
-----------------------------------------------------------------------------------*/

 jQuery(document).ready(function($) {

/*----------------------------------------------------*/
/* FitText Settings
------------------------------------------------------ */

    setTimeout(function() {
	   $('h1.responsive-headline').fitText(1, { minFontSize: '40px', maxFontSize: '90px' });
	 }, 100);


/*----------------------------------------------------*/
/* Smooth Scrolling
------------------------------------------------------ */

   $('.smoothscroll').on('click',function (e) {
	    e.preventDefault();

	    var target = this.hash,
	    $target = $(target);

	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 800, 'swing', function () {
	        window.location.hash = target;
	    });
	});


/*----------------------------------------------------*/
/* Highlight the current section in the navigation bar
------------------------------------------------------*/

	var sections = $("section");
	var navigation_links = $("#nav-wrap a");

	sections.waypoint({

      handler: function(event, direction) {

		   var active_section;

			active_section = $(this);
			if (direction === "up") active_section = active_section.prev();

			var active_link = $('#nav-wrap a[href="#' + active_section.attr("id") + '"]');

         navigation_links.parent().removeClass("current");
			active_link.parent().addClass("current");

		},
		offset: '35%'

	});


/*----------------------------------------------------*/
/*	Make sure that #header-background-image height is
/* equal to the browser height.
------------------------------------------------------ */

   $('header').css({ 'height': $(window).height() });
   $(window).on('resize', function() {

        $('header').css({ 'height': $(window).height() });
        $('body').css({ 'width': $(window).width() })
   });


/*----------------------------------------------------*/
/*	Fade In/Out Primary Navigation
------------------------------------------------------*/

   $(window).on('scroll', function() {

		var h = $('header').height();
		var y = $(window).scrollTop();
      var nav = $('#nav-wrap');

	   if ( (y > h*.20) && (y < h) && ($(window).outerWidth() > 768 ) ) {
	      nav.fadeOut('fast');
	   }
      else {
         if (y < h*.20) {
            nav.removeClass('opaque').fadeIn('fast');
         }
         else {
            nav.addClass('opaque').fadeIn('fast');
         }
      }

	});


/*----------------------------------------------------*/
/*	Modal Popup
------------------------------------------------------*/

    $('.item-wrap a').magnificPopup({

       type:'inline',
       fixedContentPos: false,
       removalDelay: 200,
       showCloseBtn: false,
       mainClass: 'mfp-fade'

    });

    $(document).on('click', '.popup-modal-dismiss', function (e) {
    		e.preventDefault();
    		$.magnificPopup.close();
    });


/*----------------------------------------------------*/
/*	Flexslider
/*----------------------------------------------------*/
   $('.flexslider').flexslider({
      namespace: "flex-",
      controlsContainer: ".flex-container",
      animation: 'slide',
      controlNav: true,
      directionNav: false,
      smoothHeight: true,
      slideshowSpeed: 7000,
      animationSpeed: 600,
      randomize: false,
   });

/*----------------------------------------------------*/
/*	contact form and validations
------------------------------------------------------*/
     $('form#contactForm button.submit').click(function()
     {
         const loader = $('#image-loader');
         const form   = $('#contactForm');

         loader.fadeIn();

         const name    = getValue('#name');
         const email   = getValue('#email');
         const subject = getValue('#subject');
         const content = getValue('#content');

         if (validateClientSide(name, email, subject, content))
         {
             let data = $.param(
                     {
                         name    : name,
                         email   : email,
                         subject : subject,
                         content : content
                     }
                 );

             $.ajax({
                 type: 'POST',
                 url: form.attr('action'),
                 data: data,
                 success: function(msg, textStatus, xhr)
                 {
                     if (xhr.status === 200)
                     {
                         handleSuccess();

                     } else {

                         handleFailure(
                             xhr.status,
                             msg
                         );
                     }
                 },
                 error: function(xhr, textStatus, errorThrown)
                 {
                     handleFailure(
                         xhr.status,
                         errorThrown
                     );
                 }
             });

         } else {
             loader.fadeOut();
             $('html, body').animate({ scrollTop: $(document).height() }, 'slow');
         }
         return false;

     });

     function getValue(selector)
     {
         return $(`#contactForm ${selector}`).val();
     }

     function handleSuccess()
     {
         $('#image-loader').fadeOut();
         $('#message-warning').hide();
         $('#contactForm').fadeOut();
         $('#message-success').fadeIn();

         // Clean fields
         $('#contactForm #name').val('');
         $('#contactForm #email').val('');
         $('#contactForm #subject').val('');
         $('#contactForm #content').val('');
     }

     function handleFailure(statusCode, msg)
     {
         $('#image-loader').fadeOut();

         if (statusCode === 422)
         {
             // Handle Unprocessable Entity (422) errors
             $('#message-warning').html('Validation failed: ' + msg)
                 .fadeIn();

         } else if (statusCode === 500) {
             // Handle Internal Server Error (500) errors
             $('#message-warning').html('Internal Server Error: ' + msg)
                 .fadeIn();

         } else {
             // Handle other errors
             $('#message-warning').html('Error (' + statusCode + '): ' + msg)
                 .fadeIn();
         }

         $('html, body').animate({ scrollTop: $(document).height() }, 'slow');
     }

     function validateClientSide(name, email, subject, content)
     {
         // Clear previous errors and reset styles
         resetValidationStyles();

         let isValid = true;

         // Check if the 'name' field is filled and does not exceed 50 characters
         if (!name || name.length > 50)
         {
             handleValidationError('name',
                 'Name is required and must not exceed 50 characters.');
             isValid = false;
         }

         // Check if the 'email' field is filled, is a valid email, and does not exceed 50 characters
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         if (!email || !emailRegex.test(email) || email.length > 50)
         {
             handleValidationError('email',
                 'Enter a valid email address (up to 50 characters).');
             isValid = false;
         }

         // Check if the 'subject' field does not exceed 100 characters (can be null)
         if (subject && subject.length > 100)
         {
             handleValidationError('subject',
                 'Subject must not exceed 100 characters.');
             isValid = false;
         }

         // Check if the 'content' field is filled and does not exceed 255 characters
         if (!content || content.length > 255)
         {
             handleValidationError('content',
                 'Content is required and must not exceed 255 characters.');
             isValid = false;
         }

         return isValid;
     }

     function resetValidationStyles()
     {
         // Clear previous errors and reset styles
         $('#message-warning').empty().hide();
         $('#contactForm input, #contactForm textarea').css('border', '1px solid #ccc');
     }

     function handleValidationError(fieldName, errorMessage)
     {
         // Show error message to the client
         $('#message-warning').append(errorMessage + '<br><br>').fadeIn();

         // Add red border to the corresponding field
         $(`#contactForm #${fieldName}`).css('border', '1px solid #ff0000');
     }
});








