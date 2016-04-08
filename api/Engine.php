<?php

class Engine
{
	// Классы API
	private $classes = array(
		'tpl'        => 'Template',
		'config'     => 'Config',
		'request'    => 'Request',
		'db'         => 'Database',
		'cart'   	 => 'Cart',
		'pages' 	 => 'Pages',
		'request'	 => 'Request',
		'images'	 => 'Images',
		'category'	 => 'Category',
		'variants'	 => 'Variants',
		'products'	 => 'Products',
		'features'	 => 'Features',
		'SimpleImage'=> 'SimpleImage',
		'orders'	 => 'Orders',
		'purchases'	 => 'Purchases',
		'users'	 	 => 'Users'
	);
	
	// Созданные объекты
	private static $objects = array();
	
	/**
	 * Конструктор оставит пустым, но определим его на случай обращения parent::__construct() в классах API
	 */
	public function __construct()
	{
		//error_reporting(E_ALL & !E_STRICT);
	}

	/**
	 * Магический метод, создает нужный объект API
	 */
	public function __get($name)
	{
		// Если такой объект уже существует, возвращает его
		if(isset(self::$objects[$name]))
		{
			return(self::$objects[$name]);
		}
		
		// Если запрошенного API не существует - ошибка
		if(!array_key_exists($name, $this->classes))
		{
			return null;
		}
		
		// Определяет имя нужного класса
		$class = $this->classes[$name];
		
		// Подключает его
		include_once(dirname(__FILE__).'/'.$class.'.php');
		
		// Сохраняет для будущих обращений к нему
		self::$objects[$name] = new $class();
		
		// Возвращает созданный объект
		return self::$objects[$name];
	}
}
