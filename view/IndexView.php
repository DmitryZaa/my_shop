<?PHP

require_once('View.php');

class IndexView extends View
{
	public function __construct()
	{
		parent::__construct();
	}
	
		function fetch()
	{
		//активная тема
		$this->tpl->tpldir = '/templates/my_theme/tpl/';
		$this->tpl->assign('theme','/templates/my_theme/');
		$this->tpl->assign('home', '/');
		$this->tpl->assign('src', '../files/catalog/');
		$this->tpl->assign('src_original', '../files/images/');
		$this->tpl->assign('page_url', '/'.$page_url);
		$this->tpl->assign('url', $_SERVER['REQUEST_URI']);
		$this->tpl->assign('session_id', session_id());
		
		$url = $this->CreateUrl();	
		
		$this->tpl->assign('url', $url);
		
		// Текущий модуль (для отображения центрального блока)
		$module = $this->request->get('module', 'string');
		$module = preg_replace("/[^A-Za-z0-9]+/", "", $module);
		
		// Если не задан
		if(empty($module))
			return false;

		// Создает соответствующий класс
		if (is_file(__DIR__ . "/$module.php"))
		{
				include_once(__DIR__ . "/$module.php");
				if (class_exists($module))
				{
					$this->main = new $module($this);
				} else return false;
		} else return false;

		// Создает основной блок страницы
		if (!$content = $this->main->fetch())
		{
			return false;
		}		

		// Передает основной блок в шаблон
		$this->tpl->assign('content', $content);
		
		// Передает название модуля в шаблон, может пригодиться
		$this->tpl->assign('module', $module);
				
		 //Текущая обертка сайта (index.tpl)

		$wrapper = $this->tpl->parse('index.tpl');

			
		if(!empty($wrapper))
			return $wrapper;
		else
			return $content;

	}
	/**
	* Записывает в сессию настройки фильтрации отображения пользователя
	*/
	public function CreateUrl()
	{
		if(strpos($_SERVER['REQUEST_URI'], '?')){
			$left = explode('?',$_SERVER['REQUEST_URI']);
			$base = explode('&', substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?')+1, strlen($_SERVER['REQUEST_URI'])));
			foreach($base as $b){
				$ar = explode('=',$b);
				if($ar[0] == 'limit'){
					$_SESSION['settings']['limit'] = $ar[1];
					unset($ar[0]);
				}elseif($ar[0] == 'sort'){
					$_SESSION['settings']['sort'] = $ar[1];
					unset($ar[0]);
				}elseif($ar[0] == 'order'){
					$_SESSION['settings']['order'] = $ar[1];
					unset($ar[0]);
				}elseif(isset($ar[0])){
					$getstr[$ar[0]] = $ar[1];
				}
			}
			if(is_array($getstr)){
				$str = '';
				foreach($getstr as $k=>$g){
					$str .= $k . '=' . $g . '&';
				}
			}
			$url = $left[0] . '?' . $str;
			
			return $url;
			
		}else{
			return $_SERVER['REQUEST_URI'] . '?';
		}
					
	}
	
}

