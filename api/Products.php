<?php

require_once('Engine.php');

class products extends Engine
{
	// 
	public function AddProduct($product = array())
	{
		// Уникальный урл
		while($this->GetProduct(array('url'=>$product['url'])))
		{
			if(preg_match('/(.+)_([0-9]+)$/', $product['url'], $parts))
				$product['url'] = $parts[1].'_'.($parts[2]+1);
			else
				$product['url'] = $product['url'].'_2';
		}
		
		$query = $this->db->placehold("INSERT INTO __products SET ?%",$product);
		$this->db->query($query);
		$id = $this->db->insert_id();
		$query = $this->db->placehold("UPDATE __products SET position=? WHERE id=? LIMIT 1",$id,$id);
		if($this->db->query($query))
			return $id;
		else
			return FALSE;
	}
	
	/**
	*  Основная выборка товаров по фильтру или ключевым словам
	* 
	* @return
	*/
	
	public function GetProducts ($filter = array())
	{	
		$page = '';
		$limit = '24';
		$visible = 'AND visible = 1';
		$category = '';
		$sort = '';
		$order = ' p.position';
		$search = '';
		
		if( !empty($filter['sort']) && !empty($filter['order']) )
			$order = $filter['sort'] . ' ' . $filter['order'] ;
		
		if( !empty($filter['limit']) )
			$limit = $filter['limit'];
		
		if( !empty($filter['page']) ){
				$page = ($filter['page'] - 1) * $limit . ', ';
		}
		
		
		if( !empty($filter['category']) ){
			$this->db->query("SELECT COUNT(id) FROM __categories WHERE parent_id =?",$filter['category']->id);
			if( $this->db->result("COUNT(id)") == 0 ){
//			$filter['category']->parent_id != 0
				$category = $this->db->placehold("AND pc.category_id = ?",$filter['category']->id);
			}else{
				$category = $this->db->placehold("AND pc.category_id IN (SELECT id FROM __categories WHERE parent_id =?)",$filter['category']->id);
			}
		}
				
		if( $filter['visible'] )
			$visible = 'visible = '.$filter['visible'];
			
		if( !empty($filter['search']) ){
			$s = addslashes(trim($filter['search']));
			$search = $this->db->placehold("AND (p.name LIKE '%$s%' OR v.code LIKE '%$s%')");
		}
		
		$query = $this->db->placehold("SELECT p.id,
											  p.url,
											  p.brand_id,
											  p.name,
											  p.annotation,
											  p.body,
											  p.meta_title,
											  p.meta_keywords,
											  p.meta_description,
											  v.code,
											  v.price,
											  v.stock
								FROM __products AS p
								JOIN __variants AS v ON p.id = v.product_id
								LEFT JOIN __products_categories AS pc ON p.id = pc.product_id
								WHERE 1
								$visible
								$category
								$search
								ORDER BY $order 
								LIMIT $page $limit");
		$this->db->query($query);
//		echo $query;
		return $this->db->results();
	}
	
	/**
	* Возвращает количество товаров согласно фильтра
	*/
	public function CountProducts($filter = array())
	{
		$limit = '24';
		$visible = 'AND visible = 1';
		$category = '';
		$sort = '';
		$order = ' p.position';
		$search = '';
		
		if( !empty($filter['sort']) && !empty($filter['order']) )
			$order = $filter['sort'] . ' ' . $filter['order'] ;
		
		if( !empty($filter['limit']) )
			$limit = $filter['limit'];
		
		if( !empty($filter['category']) ){
			$this->db->query("SELECT COUNT(id) FROM __categories WHERE parent_id =?",$filter['category']->id);
			if( $this->db->result("COUNT(id)") == 0 ){
//			$filter['category']->parent_id != 0
				$category = $this->db->placehold("AND pc.category_id = ?",$filter['category']->id);
			}else{
				$category = $this->db->placehold("AND pc.category_id IN (SELECT id FROM __categories WHERE parent_id =?)",$filter['category']->id);
			}
		}
				
		if( $filter['visible'] )
			$visible = 'visible = '.$filter['visible'];
			
		if( !empty($filter['search']) ){
			$s = addslashes(trim($filter['search']));
			$search = $this->db->placehold("AND (p.name LIKE '%$s%' OR v.code LIKE '%$s%')");
		}
		
		$query = $this->db->placehold("SELECT DISTINCT COUNT(p.id)
								FROM __products AS p
								JOIN __variants AS v ON p.id = v.product_id
								LEFT JOIN __products_categories AS pc ON p.id = pc.product_id
								WHERE 1
								$visible
								$category
								$search
								ORDER BY $order 
								LIMIT $limit");
		$this->db->query($query);
//		echo $query;
		return $this->db->result('COUNT(p.id)');
	}
	
	/**
	*  Один товар по ID или URL (вначале url, потом id)
	* 
	* @return
	*/
	
	public function GetProduct ($filter = array())
	{	
		$url = '';
		$id = '';
				
		if( !empty($filter['url']) )
			$url = $this->db->placehold('AND p.url = ?', $filter['url']);
		
		if( !empty($filter['id']) )
			$id = $filter['id'];
		
		$query = $this->db->placehold("SELECT p.id,
											  p.url,
											  p.brand_id,
											  p.name,
											  p.annotation,
											  p.body,
											  p.meta_title,
											  p.meta_keywords,
											  p.meta_description,
											  v.code,
											  v.price,
											  v.stock
								FROM __products AS p
								JOIN __variants AS v ON p.id = v.product_id
								LEFT JOIN __products_categories AS pc ON p.id = pc.product_id
								WHERE 1
								$url
								$id");
		$this->db->query($query);
//		echo $query;
		return $this->db->result();
	}
	
	
	/**
	*  Основная выборка товаров по фильтру или ключевым словам
	* 
	* @return
	*/
	
	public function GetProductsRand ($filter = array())
	{	
		$page = '';
		$limit = '24';
		$visible = 'AND visible = 1';
		$category = '';
		$sort = '';
		$order = ' p.position';
		$search = '';
		
		if( !empty($filter['limit']) )
			$limit = $filter['limit'];
				
		if( !empty($filter['category']) ){
			$this->db->query("SELECT COUNT(id) FROM __categories WHERE parent_id =?",$filter['category']->id);
			if( $this->db->result("COUNT(id)") == 0 ){
//			$filter['category']->parent_id != 0
				$category = $this->db->placehold("AND pc.category_id = ?",$filter['category']->id);
			}else{
				$category = $this->db->placehold("AND pc.category_id IN (SELECT id FROM __categories WHERE parent_id =?)",$filter['category']->id);
			}
		}
				
		if( $filter['visible'] )
			$visible = 'visible = '.$filter['visible'];
			
		$query = $this->db->placehold("SELECT p.id,
											  p.url,
											  p.brand_id,
											  p.name,
											  p.annotation,
											  p.body,
											  p.meta_title,
											  p.meta_keywords,
											  p.meta_description,
											  v.code,
											  v.price,
											  v.stock
								FROM __products AS p
								JOIN __variants AS v ON p.id = v.product_id
								LEFT JOIN __products_categories AS pc ON p.id = pc.product_id
								JOIN ( SELECT (RAND() * (SELECT MAX(id) FROM __products)) AS id ) AS r2
								WHERE 1
								AND p.id >= r2.id
								AND pc.category_id != 16
								$visible
								$category
								$search
								ORDER BY RAND() 
								LIMIT $page $limit");
		$this->db->query($query);
//		var_dump($query);
		return $this->db->results();
	}	
}