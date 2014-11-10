/* Wai for document to be ready*/
$(document).ready(function()
{
	/***********
	VARIABLES
	***********/
	var processUrl = "includes/Process.class.php";
	var allMenus   = $('*[id*=-Menu]');
	var msgTimer;

	/*****************************************************
	USER MENUS SHOW AND HIDE ON MOUSEENTER/MOUSELEAVE
	*****************************************************/
	$(".ShowMenu").mouseenter(function (e)
	{
	// Side down the menu.
	$('#' + $(this).attr('data-menu') ).slideToggle(200);

	}).mouseleave(function (e)
	{
	// hide the menu
	$('#' + $(this).attr('data-menu') ).hide();
	});

	/*****************************************************
	AUTO COMPLETE FORMS
	*****************************************************/
	$( "#searchItem" ).autocomplete(
	{
	  source: function(request, response)
	  {
		$.ajax(
		{
		  url: processUrl,
		  dataType: "json",
		  data:
		  {
			term: request.term,
			searchFrom: $("#searchFrom").val(), 
			searchWhat: $("#searchWhat").val()
		   },
			success: function(data) 
			{
			  response(data);
			}
		})
	  }
	, minLength: 3
	  //, 
	  //select: function( event, ui ) 
	  //{
	   // var alt = $(this).val(ui.item.label);
	   // this.form.submit();
	  //}
	});

	/* Submit form */
	$(document).on('submit','form', function( e )
	{
		// define the form that has been submited
		var formClicked = $( this );

		// disable anything that was empty
		$(this).children("select, input").each(function()
		{
			if($(this).val() == "")
			$(this).attr("disabled", true);
		});

		// If its a form that needs to be submited run it (will refresh page)
		if ( !$( this.ajaxrequest ).val() ) { return; }                

		$.post( processUrl, $(this).serialize() ).done( function( data ) 
		{
			// var returnedJson = $.parseJSON( data ); Unable this if the json is not passed right
			//alert( 'The data: ' + returnedJson ); // Enable for testing
			//alert(JSON.stringify(data));
			/* Check if the submition was successful */

			/* Check If we want to remove an element & if the element exists */
			if(data.remove !== '' && $('#'+data.remove).length !== 0 )
				$('#'+data.remove).fadeOut('Slow', function() {$('#'+data.remove).remove();});

			// if the there was no errors procced
			if( data.error_success == 1 )
			{
				// Empty the error or success 
				$('#error_success').empty();

				$(formClicked).find('input[type=text], select').css({"border": "1px solid green"});
				// clearn any errors 
				$('.error_msg').fadeOut('Slow').empty();

				// Slide down the msg box
				$('#message_box').slideDown( );
				// Add the msg to the box                     
				$('#message_box_msg' ).html( '<span class="loading-icon"> Loading </span>' ); 

				$( '#error_success' ).removeClass( ).addClass( 'success' ).append( 'Success' );
				// Slide down the msg box
				$( '#message_box' ).slideDown( );
				// Add the msg to the box
				$( '#message_box_msg' ).html( data.msg );

				// Close the Mag window after 10 seconds.
				clearTimeout( msgTimer );
				msgTimer = setTimeout(function(){ $('#message_box').fadeOut('Slow');}, 7000);

			// errors were found so display them on the page
			} else {
				$(formClicked).find( '.' + data.append ).html( data.msg ).fadeIn('Slow');
				$(formClicked).find('input[name="' + data.append + '"]').css({"border": "1px solid red"}).focus();
				console.log( $(formClicked).find('input[name="character_name"]').val() );
			}
	  
		}) // End .done ( function (data ) )
	  .fail( function(){ alert( 'There was a problem.' ); })
	// Prevent the form from being submited.  
	return false; 
	}); // End on submit.

	/***************/
	/* Message Box */
	/***************/
	$( window ).scroll( function( )   // Scroll msg with window
	{
	  $( '#message_box' ).animate( { top:$( window ).scrollTop( )+"px" },{ queue: false, duration: 350 }) ;  
	});

	// Close button was click
	$( 'button[name=close-msg]' ).click( function( ) { $( '#message_box' ).fadeOut( 'Slow' ); });


	$(function() 
	{
	var moveLeft = 0;
	var moveDown = 0;
	$('a.popper').hover(function(e) 
	{
	  var itemId = $(this).attr('data-popbox');
	  var target = '#item_' + itemId;

	  $.post( processUrl, { item_id: itemId, todo: 'itemToolTip' } )
	  .done(function( data ) 
	  {
		$( target ).html( data );

	  });
	  $( target ).show();

	  moveLeft =   35; // change to $(this).outerWidth(); for showing entire element
	  moveDown = ( $(target).outerHeight() / 2 );
	}, function() 
	{
	  var target = '#item_' + ($(this).attr('data-popbox'));
	  $(target).hide();
	});

	$('a.popper').mousemove(function(e) 
	{
	  var target = '#item_' + ($(this).attr('data-popbox'));
	   
	  leftD = e.pageX + parseInt(moveLeft);
	  maxRight = leftD + $(target).outerWidth();
	  windowLeft = $(window).width() - 40;
	  windowRight = 0;
	  maxLeft = e.pageX - (parseInt(moveLeft) + $(target).outerWidth() + 20);
	   
	  if(maxRight > windowLeft && maxLeft > windowRight)
		leftD = maxLeft;
	 
	  topD = e.pageY - parseInt(moveDown);
	  maxBottom = parseInt(e.pageY + parseInt(moveDown) + 20);
	  windowBottom = parseInt(parseInt($(document).scrollTop()) + parseInt($(window).height()));
	  maxTop = topD;
	  windowTop = parseInt($(document).scrollTop());

	  if(maxBottom > windowBottom)
		topD = windowBottom - $(target).outerHeight() - 20;
	  else if(maxTop < windowTop)
		topD = windowTop + 20;
	 
	  $(target).css('top', topD).css('left', leftD);
	});
	});


	/* Check if a Box has a cookie */
	$("div[id^='ShowBox_']").each(function()
	{
	var id   = $(this).attr('id'); // The ID
	var link = id.replace('ShowBox_','');

	if( $.cookie( id ) == null )
	{
	  $( 'a#' + link ).text('Hide');
	  $( '#'  + id ).slideDown('slow'); // No cookie, Hide the box
	} else{
	  $( 'a#' + link ).text('Show');
	  $( '#' + id ).hide(); // Cookie found, Show the box
	}

	});

	/* toggle Link Clicked */
	$( "a.toggle" ).click(function(e) 
	{
	var id = $(this).attr('id'); // The ID
	var linkTxt = $(e.target).text();

	/* Check to see if a cookie exists for this click */
	if( $.cookie( 'ShowBox_' + id ) == null ) 
	  $.cookie( 'ShowBox_' + id, '1',  { expires: 7, path: '/' } ); // No cookie, Create one.
	else
	  $.cookie( 'ShowBox_' + id, null, { expires: 0, path: '/' } ); // Cookie Found, destroy it.

	/* Toggle arrow up & down */
	if( linkTxt == 'Show') $(e.target).text('Hide');
	if( linkTxt == 'Hide') $(e.target).text('Show');
	$('#ShowBox_' + id).toggle(500);

	return false;
	});

	/*
	$("#ShowBox_ManageAlts").slick({
	slidesToShow: 5,
	slidesToScroll: 5,
	dots: false,
	focusOnSelect: true
	});

	/******************************
	  BOTTOM SCROLL TOP BUTTON
	******************************/
	// declare variable
	var scrollTop = $(".scrollTop");

	$(window).scroll(function(){
		// declare variable
	var topPos = $(this).scrollTop();

	// if user scrolls down - show scroll to top button
	if(topPos > 100)
	  $(scrollTop).fadeIn( "slow" );
	else
	  $(scrollTop).fadeOut( "slow" );

	}); // scroll END

	//Click event to scroll to top
	$(scrollTop).click(function(){
	$('html, body').animate({scrollTop : 0},800);
	return false;

	}); // click() scroll top EMD

}); // document ready ends