<?php

require_once('Engine.php');

class cart extends Engine
{
	
	public function GetCart()
	{
		if(!empty($_SESSION['cart']))
		{
			$cart = array();
			$total_price = 0;
			$i = 0;
			foreach($_SESSION['cart'] as $c)
			{
				$cart[$i] = new stdClass();
				$cart[$i]->pid = $c['pid'];
				$cart[$i]->quantity = $c['quantity'];
				$cart[$i]->size = $c['size'];
				$cart[$i]->color = $c['color'];
				$cart[$i]->key = $this->CreateKey($c['color'], $c['pid'], $c['size']);
				
				$this->db->query("SELECT p.url, p.name, v.code, v.price 
										FROM __products AS p
										JOIN __variants AS v ON p.id = v.product_id
										WHERE p.id=? LIMIT 1",$c['pid']);
				$prod = $this->db->result();
				$cart[$i]->url = $prod->url;
				$cart[$i]->name = $prod->name;
				$cart[$i]->code = $prod->code;
				$cart[$i]->price = $prod->price;
				
				$this->db->query("SELECT filename FROM __images WHERE product_id=? LIMIT 1",$c['pid']);
				$filename = $this->db->result('filename');
				
				// подготовка изображения
				$original = dirname(dirname(__FILE__)).'/files/images/'.$filename;
				$new_im = dirname(dirname(__FILE__)).'/files/cart/'.$filename;
				if( !file_exists(dirname(dirname(__FILE__)).'/files/cart/'.$filename) ){
					$this->SimpleImage->load($original);
					$this->SimpleImage->resizeToHeight(90);
					$this->SimpleImage->save($new_im);
				}
				
				$cart[$i]->filename = $filename;
				$total_price += $cart[$i]->price * $cart[$i]->quantity;
				$i++;
			}
			
			$obj = new stdClass;
			$obj->total_price = $total_price;
			$obj->cart = $cart;
			return $obj;
		}else{
			return FALSE;
		}
		
	}
	
	public function GetCartData()
	{
		$key = $this->CreateKey($color, $pid, $size);
		if( isset($_SESSION['cart']) ){
			$data = new stdClass;
			$data->quantity = 0;
			$data->money = 0;
			
			foreach($_SESSION['cart'] as $v)
			{
				$data->quantity += $v['quantity'];

				$this->db->query("SELECT price 
							  	  FROM __variants 
							  	  WHERE product_id = ?",$v['pid']);
				$res = $this->db->result('price');
				$data->money += $res * $v['quantity'];
			}
			return $data;
		}
		
	}
	
	public function AddItem($pid, $quantity, $size='', $color='')
	{
		$key = $this->CreateKey($color, $pid, $size);
				
		if( !isset($_SESSION['cart'][$key]) ){
			
			$_SESSION['cart'][$key] = array('pid'=>$pid, 'quantity'=>$quantity, 'size'=>$size, 'color'=>$color);
		
		}else{
			
			$new_quantity = $_SESSION['cart'][$key]['quantity'] + $quantity;			
			$_SESSION['cart'][$key] = array('pid'=>$pid, 'quantity'=>$new_quantity, 'size'=>$size, 'color'=>$color);
		}
	}
	
	public function UpdateItem($pid, $quantity, $size='', $color='')
	{
		$key = $this->CreateKey($color, $pid, $size);
				
		$_SESSION['cart'][$key] = array('pid'=>$pid, 'quantity'=>$quantity, 'size'=>$size, 'color'=>$color);
		
	}
	
	public function DeleteItem($pid, $size='', $color='')
	{
		$key = $this->CreateKey($color, $pid, $size);
				
		unset($_SESSION['cart'][$key]);
		
	}
	
	public function EmptyCart()
	{
		unset($_SESSION['cart']);
		
	}
	
	private function CreateKey($color, $pid, $size)
	{
		$c  = $this->str2url($color);
		return trim($pid).trim($size).$c;
	}
	
	private function transliterate($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    private function str2url($str)
    {
        $str = $this->transliterate($str);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        $str = trim($str, "-");
        return $str;
    }
}