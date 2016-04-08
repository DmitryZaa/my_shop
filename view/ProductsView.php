<?php

require_once('View.php');

class ProductsView extends View 
{
	function fetch()
	{
	
		$filter = array();
		
		if($this->request->get('search')){
			$filter['search'] = $this->request->get('search');
			$this->tpl->assign('search',$filter['search']);
		}
		
		if( isset($_SESSION['settings']['limit']) )
			$filter['limit'] = $_SESSION['settings']['limit'];
		else 
			$filter['limit'] = 24;
		
		$this->tpl->assign('limit',$filter['limit']);
		
		if( isset($_SESSION['settings']['sort']) && $_SESSION['settings']['sort'] == 'none' ){
			unset($_SESSION['settings']['sort']);
			unset($_SESSION['settings']['order']);			
		}
			
		if( isset($_SESSION['settings']['sort']) ){
			$filter['sort'] = $_SESSION['settings']['sort'];
			$filter['order'] = $_SESSION['settings']['order'];
			$this->tpl->assign('sort',$filter['sort']);			
			$this->tpl->assign('order',$filter['order']);			
		}
		if( $this->request->get('page') )
			$filter['page'] = $this->request->get('page');			
		else 
			$filter['page'] = '1';
			
		$this->tpl->assign('current_page',$filter['page']);

		if( $cat_url = $this->request->get('category') ){
			$act_cat = $this->category->GetIdCategoryByURL($cat_url);
			$filter['category'] = $act_cat;
			$this->tpl->assign('active_category', $act_cat->name);
		}
		
		// количество товаров  пагинация
		$products_count = $this->products->CountProducts($filter);
		$this->tpl->assign('products_count', $products_count);
		$this->tpl->assign('pages', ceil($products_count/$filter['limit']));
		
		$this->tpl->assign('start_show',(($filter['page']-1) * $filter['limit'])+1);
		$end_show = ($filter['page']-1) * $filter['limit']+($filter['limit']+1);
		if($end_show > $products_count) $end_show = $products_count;
		$this->tpl->assign('end_show',$end_show);
			
		$this->tpl->assign('page', $_SESSION['settings']['page']);
		
		$products = $this->products->GetProducts($filter);
		
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
		
		return $this->tpl->parse('products.tpl');
	}
}