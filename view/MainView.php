<?php

require_once('View.php');

class MainView extends View 
{
	function fetch()
	{
		$filter = array();
		$filter['limit'] = '14';
		$products = $this->products->GetProductsRand($filter);
		
		foreach( $products as &$p ){
		
			$p->options = $this->features->GetOptionsForProduct($p->id);
						
			$images = $this->images->GetProductImages($p->id);			
			$p->image = $images[0];
	// подготовка изображений
			$original = dirname(dirname(__FILE__)).'/files/images/'.$p->image->filename;
			$new_im = dirname(dirname(__FILE__)).'/files/catalog/'.$p->image->filename;
			if( !file_exists(dirname(dirname(__FILE__)).'/files/catalog/'.$p->image->filename) ){
				$this->SimpleImage->load($original);
				$this->SimpleImage->resizeToHeight(240);
				$this->SimpleImage->save($new_im);
			}
		}
		
		$this->tpl->assign('cart', $this->cart->GetCartData());
		
		$this->tpl->assign('products', $products);
		
				
		$categories = $this->category->GetTree();
		
		$this->tpl->assign('categories', $categories);
		return $this->tpl->parse('main.tpl');
	}
}