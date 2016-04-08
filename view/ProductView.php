<?php

require_once('View.php');

class ProductView extends View 
{
	function fetch()
	{
		
		$product_url = $this->request->get('product_url');
		
		$product = $this->products->GetProduct(array('url'=>$product_url));
		
		$product->options = $this->features->GetOptionsForProduct($product->id);
						
		$product->images = $this->images->GetProductImages($product->id);	
		
		$this->tpl->assign('cart', $this->cart->GetCartData());
		
		$this->tpl->assign('product', $product);
		// топ меню
		$categories = $this->category->GetTree();
		$this->tpl->assign('categories', $categories);
		
		return $this->tpl->parse('product.tpl');
	}
}