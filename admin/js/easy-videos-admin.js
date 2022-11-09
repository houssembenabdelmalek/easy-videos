(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
				popup.innerHTML = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ video_ID +'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				popup.classList.toggle("show");
				
		});
		$('.thumbnail').click(function(e){
			e.preventDefault();
				var video_ID = $(this).attr('data-video');
				var popup = document.getElementById("myPopup");
				popup.innerHTML = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ video_ID +'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				popup.classList.toggle("show");
				
		});

		$('.importvideo').click(function(e){
			
			//var urlapi = document.getElementById("url").value;
			//alert(dataId+" \n "+dataTitle+" \n "+dataDesc+" \n "+dataDate+" \n "+dataThumb+" \n ");
			e.preventDefault();

            // L'URL qui réceptionne les requêtes Ajax dans l'attribut "action" de <form>
            const ajaxurl = $(this).attr('data-ajaxurl');

            // Les données de notre formulaire
			// ⚠️ Ne changez pas le nom "action" !
            const data = {
                action: $(this).attr('data-action'), 
                dataId: $(this).attr("data-id"),
				dataTitle: $(this).attr("data-title"),
				dataDesc: $(this).attr("data-desc"),
				dataDate: $(this).attr("data-date"),
				dataThumb: $(this).attr("data-thumb"),
				nonce: $(this).attr("data-nonce"),
            }

            // Pour vérifier qu'on a bien récupéré les données
            //console.log('ajaxurl:'+ajaxurl);
            //console.log(data);
			fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: new URLSearchParams(data),
            })
            .then(response => response.json())
            .then(response => {
                console.log(response);

                // En cas d'erreur
                if (!response.success) {
                    alert('err: '+response.data);
                    return;
                }

                // Et en cas de réussite
				if(response.data == 'success') { $(this).html('Remove'); } else { $(this).html('Import'); }
                
            });
        });
	});

	 

})( jQuery );
