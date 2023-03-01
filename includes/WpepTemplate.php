<?php

//namespace Plugin\WpepEbay;

class WpepTemplate
{

	public function get_item_template($display, $data){
	
		if($data['cache']['count'] < 1){
			return $this->template_empty_results();
		}
	
		switch($display) {
			case 'table':
				$template = $this->template_table($data);
				break;
			case 'responsive':
				$template = $this->template_responsive($data);
				break;
			case 'flip':
				$template = $this->template_flip_card($data);
				break;
			case 'poster':
				$template = $this->template_poster($data);
				break;
			case 'image':
				$template = $this->template_image($data);
				break;
			case 'slideshow':
				$template = $this->template_slideshow($data);
				break;
		}
		return $template;
	}
	
	public function template_empty_results(){
		$data = [
			'header'	=> '<div class="wpep-display-container"><div>',
			'body'		=> '<h2>No results found...</h2>',
			'footer'	=> '</div></div>'
		];
		
		return $data; 
	}
	
	public function template_table($data){
		
		$snippet = '<thead><tr><td>Image</td><td>Title</a></td><td>Price</td></tr>';
		$template = '<tr><td class="wpep-img-col"><img src="%s"></td><td><a href="%s" target="_blank">%s</a></td><td>$%.2f</td></tr>';
		
		foreach($data['cards'] as $key => $item){
			$snippet .= sprintf($template, $item['pic'], $item['url'], $item['title'], $item['price']);
		}
		$header = '<div class="wpep-display-container"><table class="wpep-module-table ajax-render">';
		
		$footer = '</table></div>';
		
		$data = [
			'header'	=> $header,
			'body'		=> $snippet,
			'footer'	=> $footer
		];
		
		return $data; 
	}
		
	public function template_flip_card($data){

		$class = 'card' . ' card-'. $data['module']['columns'] . ' card-' .$data['module']['template_aspect']. ' ' .$data['module']['imagesize'] . ' '. $data['module']['orientation'];
		$gridCol = 'grid-wrap-'. $data['module']['columns'];
		$counter = 1;
		$template = '<div class="'.$class.'" data-count="%s">
			<div class="face face--front" style="background: url(%s);"></div>
			<div class="face face--back">
			<div class="back-card">
				<div class="back-card-overlay" >
				<img src="%s" /></div>
				<div class="back-card-info">
				<h4><b>%s</b></h4> 
				</div>
				<div class="back-card-action">
			  	<p>$%.2f</p>
			  	<a href="%s" target="_blank">Buy Now</a> 
				</div>
		  	</div>
			</div>
	  	</div>';
			  	
		$snippet = '';
		
		foreach($data['cards'] as $key => $card){
			$snippet .= sprintf($template, $counter, $card['pic'], $card['pic'],  $card['title'], $card['price'], $card['url']);
			
		}

		$footer = '</div></div>';
		
		$data = [
			'header'	=> $this->get_template_header('flip-card', $gridCol),
			'body'		=> $snippet,
			'footer'	=> $footer
		];
		
		return $data;
	}
	
	public function template_image($data){
		$class = 'image-card' . ' card-'. $data['module']['template_aspect']. ' ' . $data['module']['orientation']. ' ' .$data['module']['imagesize'];
		$gridCol ='grid-wrap-'.$data['module']['columns'];
		
		$template = '<div class="wpep-image-card '.$class.'">
			<a href="%s" target="_blank">
				<div class="wpep-image-cards">
					<div class="wpep-imgBx">
						<img src="%s" alt="images">
					</div>
					<div class="wpep-image-details">
						<h2>%s<br><span>$%.2f</span></h2>
					</div>
			  	</div>
			</a>
	  	</div>';
		
		$snippet = '';
		
		foreach($data['cards'] as $key => $card){
			$snippet .= sprintf($template, $card['url'], $card['pic'], $card['title'], $card['price']);
		}
		
		$data = [
			'header'	=> $this->get_template_header('grid', $gridCol),
			'body'		=> $snippet,
			'footer'	=> '</div></div>'
		];
		
		return $data;
	}
	
	public function template_poster($data){
		$gridCol ='grid-wrap-'.$data['module']['columns'];
		$class = 'card' . ' card-'. $data['module']['columns'] . ' card-' .$data['module']['template_aspect']. ' ' .$data['module']['imagesize'] . ' '. $data['module']['orientation'];
		
		$template = '<div class="poster-card '.$class.'">
			<a href="%s" target="_blank">
		  	<div class="poster-image" style="background-image:url(%s);">
				<div class="poster-image-tag">
			  	$%.2f
				</div>
		  	</div>
			</a>
			<div class="poster-description">
		  	
		  	<p>%s</p>
			</div>
			<a href="%s" target="_blank" class="wpep-poster-buy-btn">
		  	Buy on Ebay
			</a>
	  	</div>';
		
		$snippet = '';
		
		foreach($data['cards'] as $key => $card){
			$snippet .= sprintf($template, $card['url'], $card['pic'],$card['price'], $card['title'], $card['url']);
		}
		
		$data = [
			'header'	=> $this->get_template_header('figure', $gridCol),
			'body'		=> $snippet,
			'footer'	=> '</div></div>'
		];
		
		return $data;
	}
	
	
	public function template_slideshow($data){
		$class = 'card' . ' card-' .$data['module']['template_aspect']. ' ' .$data['module']['imagesize'] . ' '. $data['module']['orientation'];
		$template = '<div class="slide-card">
			<img class="'.$class.'" src="%s" />
		
			<a href="%s" target="_blank" class="wpep-slider-buy-btn">
			  Buy on Ebay
			</a>
		  </div>';
		  
		  $snippet = '';
		  
		  foreach($data['cards'] as $key => $card){
			  $snippet .= sprintf($template, $card['pic'], $card['url']);
//			  $snippet .= sprintf($template, $card['url'], $card['pic'], $card['price'], $card['title'], $card['url']);
		  }
		
		return [
			'header'	=> $this->get_template_header('slideshow'),
			'body'		=> $snippet,
			'footer'	=> '</div>'
		];
	}
	
	public function paginationHeader(){
		return '<div class="wpep-pagination-container">';
	}
	
	public function get_template_header($wrapper, $custom=''){
		
		return '<div class="wpep-display-container"><div class="wpep-'.$wrapper.'-wrapper '.$custom.' ajax-render">';
	}
	
	/* Takes pagination array and outputs the HTML container with links
 	*	Ebay pagination 
 	* $pagination = array( pageNumber, totalPages, entriesPerPage, totalEntries)
 	*/
	public function template_pagination($pagination){

		//@TODO when wpepp is set to number larger than max page, we need to update it to last page
		// no errors, but should be handled properly

			$nextpage = $prevpage = $pagination['pageNumber'];
			$start = 1;
			if($pagination['pageNumber'] > 4){ 
				$start = $pagination['pageNumber'] -4;
			}
			$end = $start+8;
			
			if($end > $pagination['totalPages']){
				$end = $pagination['totalPages']+1;
			};

			if($pagination['totalPages'] > $pagination['pageNumber']){
				$nextpage++;
			}
			if($pagination['pageNumber'] > 1){
				$prevpage--;
			}
			
			$output = '<div class="wpep-pagination-wrapper" data-page-id="'.$pagination['pageNumber'].'"><ul class="wpep-pagination-list">';
					
			if($prevpage < $pagination['pageNumber']){
				$output .= '<li><a href="#" class="wpep-paginate-link" data-page='.$prevpage.'>Prev</a></li>';	
			}
					
					
			for($i = $start; $i < $end; $i++){
				$class = ($i == $pagination['pageNumber'])? 'active': 'inactive';
				$output .= '<li><a href="#" class="wpep-paginate-link '.$class.'" data-page='.$i.'>'. $i . '</a></li>';
			}
			
			if($nextpage > $pagination['pageNumber']){
				$output .= '<li><a href="#" class="wpep-paginate-link" data-page='.$nextpage.'>Next</a></li>';
			}
			
			$output .= '</ul></div>';
						
			return $output;

	}
	
	
}