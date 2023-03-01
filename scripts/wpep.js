jQuery(document).ready(function(){
  
  if ( jQuery('.wpep-slideshow-wrapper').not('.slick-initialized').length >= 1) {
   //Init Slick
   jQuery('.wpep-slideshow-wrapper').not('.slick-initialized').each(function( index ) {
     // grab data attr for slider
     var columns = jQuery( this ).closest('.wpep-module-container').data('slider');
     console.log( "columns: " + columns );
     
     // init slider with attributes
     jQuery( this ).slick({
        slidesToShow: columns,
        draggable: true,
        swipeToSlide: true,
        slidesToScroll: 1,
        dots: false,
        centerMode: true,
        centerPadding: '60px',
        autoplay: true,
        autoplaySpeed: 3000,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1,
              infinite: true,
              dots: false
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
          // You can unslick at a given breakpoint now by adding:
          // settings: "unslick"
          // instead of a settings object
        ]
      });
     
   });
   
  }
  
  function get_data_attributes(el){
    const props = {
      _ajax_nonce: wpep.nonce,     //nonce
      action: "wpep_search_ajax"
    };
    // loop through each data attribute
    jQuery.each( el.data(), function(key, value) {
        props[key] = value;
    });
    
    // check for search
    if(el.find('input.wpep-search-input').length){
     props['search'] =el.find('input.wpep-search-input').val(); 
    }
    //return all data attributes
    return props;
  }
  
  jQuery(document).on('click', 'a.wpep-paginate-link', function(event){
    event.preventDefault();
    event.stopImmediatePropagation();
    
    let container = jQuery(this).closest('.wpep-module-container');
    
    let datas = get_data_attributes(container);
    
    datas['pagenum'] =  jQuery(this).data('page');
    
    updateEl = container.find('.ajax-render');
    updateEl.addClass('wpep-faded');
    
    jQuery.post(wpep.ajaxUrl, datas, function(response) {
        var data = jQuery.parseJSON(response);
        
        // Update Pagination list
        container.find('.wpep-pagination-list').html(data.pagination);
        
        // update the element with new content
        updateEl.html(data.result);
        
        updateEl.removeClass('wpep-faded');
        
        // trigger wordpress callback for ajax data load
        jQuery( document.body ).trigger( 'post-load' );  
    });
    
  });
  
  jQuery( '.wpep-search-form' ).submit(function( event ) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    id = jQuery(this).attr('id');
    let container = jQuery(this).closest('.wpep-module-container');
    let data = get_data_attributes(container);
    
    pagination = container.find('div.wpep-pagination-container');
    updateEl = container.find('.ajax-render');
    updateEl.addClass('wpep-faded');
    let datas = get_data_attributes(container);

    datas['search'] = jQuery(this).find('input.wpep-search-input').val();
    
    jQuery.post(wpep.ajaxUrl, datas , function(response) {
        var data = jQuery.parseJSON(response);
        
        // Update Pagination list
        container.find('.wpep-pagination-list').html(data.pagination);
        
        // Update cards
        // update the element with new content
        updateEl.html(data.result);
        updateEl.removeClass('wpep-faded');
        
        // trigger wordpress callback for ajax data load
        jQuery( document.body ).trigger( 'post-load' );        

      }
    );
  });
  
});