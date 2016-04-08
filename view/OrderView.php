<?php

require_once('View.php');

class OrderView extends View 
{
	function fetch()
	{
		$url = $this->request->get('url');
		
		$order = $this->orders->GetOrder($url);
		
		$purchases = $this->purchases->GetPurchases($order->id);
		
		foreach($purchases as &$p){
			$p->filename = $this->images->GetProductImage($p->product_id);
		}

		$this->tpl->assign('order', $order);
		$this->tpl->assign('purchases', $purchases);
		
		$categories = $this->category->GetTree();
		$this->tpl->assign('categories', $categories);
		
		return $this->tpl->parse("order.tpl");
	}

}