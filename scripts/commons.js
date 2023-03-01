/* input used for search
<input type="text" data-keyword="" class="wpep-search-box" id="ebay-filter-input" action="javascript:handleIt()" onkeyup="anOverride()" placeholder="Search for Names, dates, stack, collection, team..">
*/
function wpepSearchFunction(e){
	  //execute search
		var searchterm, keyword, fullterm;
		searchterm = document.getElementById('ebay-filter-input').value
		keyword = document.getElementById('ebay-filter-input').getAttribute('data-keyword');
		// get keyword
		console.log('keyword: '+ keyword);
		console.log('search term: ' +searchterm);
		fullterm = keyword + " " + searchterm;
		console.log('fullterm: '+ fullterm);
		
		// insert keyword with search term
		jQuery('.an-search-box').html(fullterm);
		
		alert('trigger');
		//jQuery('.an-search-submit').trigger('click');
	e.preventDefault()
}
	
function runEbayFilter() {
  		// Declare variables
  		var txtValue, mainString;
  		txtValue = document.getElementById('ebay-filter-input').value;
		
		if(txtValue.length < 1){
			//show all
		   jQuery( '.an-title' ).parent().parent().show();  
		}
		
		jQuery('.an-title a').each(function( index ) {
			mainString = jQuery( this ).text();
			console.log( index + ": " + mainString);
			if( mainString.toLowerCase().includes( txtValue.toLowerCase() )) {
		   		jQuery( this ).parent().parent().show();
		   	} else {
		   		jQuery( this ).parent().parent().hide();
		   	}
			
		});
		
}
