(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $(document).ready(function(){
		$('.popup').click(function(e){
			e.preventDefault();
				var video_ID = $(this).attr('data-video');
				var popup = document.getElementById("myPopup");
				popup.innerHTML = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'+ video_ID +'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				popup.classList.toggle("show");
				
		});
		$('.thumbnail').click(function(e){
			e.preventDefault();
				var video_ID = $(this).attr('data-video');
				var popup = document.getElementById("myPopup");
				popup.innerHTML = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'+ video_ID +'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				popup.classList.toggle("show");
				
		});
	});
})( jQuery );

			
