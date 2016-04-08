<?PHP

require_once('api/Engine.php');

class View extends Engine
{
//	доступность следующих переменных в любом View 
	public $user;
	public $page;
	
//	 Класс View похож на синглтон, храним статически его инстанс 
	private static $view_instance;
	
	public function __construct()
	{
		parent::__construct();

	}
}