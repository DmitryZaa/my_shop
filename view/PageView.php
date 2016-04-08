<?php

require_once('View.php');

class PageView extends View 
{
	function fetch()
	{
		$page_url = $this->request->get('page_url');
		
		$page = $this->pages->GetPage($page_url);
		
		$this->tpl->assign('page', $page);
		
		$categories = $this->category->GetTree();
		$this->tpl->assign('categories', $categories);
		
		return $this->tpl->parse("page.tpl");
	}

}