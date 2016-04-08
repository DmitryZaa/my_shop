<?php

require_once('View.php');

class CartView extends View 
{
	function fetch()
	{
	// операции с корзиной
		if($this->request->method('post'))
		{
			if($this->request->post('action') == 'add_item'){
				$pid = $this->request->post('pid');
				$quantity = $this->request->post('quantity');
				$color = $this->request->post('color');
				$size = $this->request->post('size');
				
				$this->cart->AddItem($pid, $quantity, $size, $color);
				$data = $this->cart->GetCartData();
				
				$json = json_encode($data);
				echo $json;
		// выход
				exit;				
				
			}
			elseif($this->request->post('action')  == 'ajax_cart'){
				
				$cart = $this->cart->GetCart();
				$this->tpl->assign('cart_value', $cart->cart);
				$this->tpl->assign('total_price', $cart->total_price);
				$this->tpl->assign('cart', $this->cart->GetCartData());
				
				echo $this->tpl->parse("ajax/cart.tpl");
		// выход	
				exit;
			}
			
			elseif($this->request->post('key')){
				$key = $this->request->post('key');
				$quantity = $this->request->post('quantity');
				$danger = $this->request->post('danger');
				
				if( count($key) == count($quantity) &&
					count($key) == count($danger)){
					
					for($i=0; $i<count($key); $i++){
						if($danger[$i] == '1'){
							
							unset($_SESSION['cart'][$key[$i]]);
							
						}else{
							
							$_SESSION['cart'][$key[$i]]['quantity'] = $quantity[$i];
							
						}
					}
				}
			}
				
		}
		
// Оформление заказа
		if($this->request->get('order') == 'new'){
			
			$cart_new = $this->cart->GetCart();
			
			$user = new stdClass;
			$user->firstname = $this->request->post('firstname','string');
			$user->lastname = $this->request->post('lastname','string');
			$user->email = $this->request->post('email');
			$user->phone = $this->request->post('telephone','string');
			$user->enabled = 1;
			
			$user_id = $this->users->AddUser($user);
			
			$order = new stdClass;
			$order->address = '';
			$order->address = $this->request->post('country','string');
			$order->address .= $this->request->post('address_1','string');
			$order->address .= ' '.$this->request->post('address_2','string');
			$order->address .= ' '.$this->request->post('area','string');
			$order->address .= ' '.$this->request->post('Street','string');
			$order->address .= ' '.$this->request->post('house','string');
			$order->address .= ' '.$this->request->post('flat','string');
			$order->note = $this->request->post('note','string');
			$order->total_price = $cart_new->total_price;
			$order->name = $user->firstname . ' ' .$user->lastname;
			$order->phone = $user->phone;
			$order->email = $user->email;
			$order->user_id = $user_id;
			$order->url = md5(md5(mt_rand(100000,999999)));
			
			$order_id = $this->orders->AddOrder($order);
			
			$purchase = array();
			for($i = 0; $i < count($cart_new->cart); $i++){
				$purchase = new stdClass;
				
				$purchase->order_id = $order_id;
				$purchase->product_id = $cart_new->cart[$i]->pid;
				$purchase->price = $cart_new->cart[$i]->price;
				$purchase->quantity = $cart_new->cart[$i]->quantity;
				$purchase->code = $cart_new->cart[$i]->code;
				$purchase->product_name = $cart_new->cart[$i]->name;
				$purchase->options = $cart_new->cart[$i]->size . '|' . $cart_new->cart[$i]->color;
				$this->purchases->AddPurchase($purchase);
			}			
			
			$this->cart->EmptyCart();
			header("Location: /order/".$order->url);
		}
//	выдача корзины		
		$cart = $this->cart->GetCart();

		$this->tpl->assign('cart_value', $cart->cart);
		$this->tpl->assign('total_price', $cart->total_price);
		$this->tpl->assign('cart', $this->cart->GetCartData());
		$this->tpl->assign('here_is_cart', '1');
		
		$categories = $this->category->GetTree();
		$this->tpl->assign('categories', $categories);
		
//		mail ($order->email, 'Заказ №'.$order_id.' принят', 'Мы свяжемся с вами');    
		
		return $this->tpl->parse("cart.tpl");
	}
} 