<?php
namespace Dulit\Core;
trait trait1
{
	function callBack($info){}
}
interface interface1
{
	const BACD = 5;
	public function func1($arg1,$arg2);
	public function func2($arg1,$arg2);
}

interface interface2
{
	public function  Method3($arg1 = 2.8 ,$arg2 = 1);
	public function Method2(&$arg1);
}

abstract class ParentClass 
{
	const cBACD = 5;
	const cBACD1 = 0.5;
	const cBACD2 = false;
	const cBACD3 = 'false';
	const cBACD4 = NULL;
	function myfunc(){}
}


class Topclass extends ParentClass implements interface1
{
	protected $property8 = 1;

	function myfunc()
	{
		return 0;
	}

	public function func1($arg1,$arg2){}
	public function func2($arg1,$arg2){}
	
}

final class Application extends Topclass implements interface2
{
	public function __construct(){}
	public function func1($arg1,$arg2){}
	public function func2($arg1,$arg2){}
	public static function Method1(){}
	public final function Method2(&$arg1){}
	public function Method3($arg1 = 2.8 ,$arg2 = 1){}

	protected function Method4(){}
	protected function Method5($arg1){}
	protected function Method6(array $arg1 = NULL,$arg2 = 1){}

	/**
 * Adds a route to the router that only match if the HTTP method is PATCH
 * @return string
 */
	private function Method7(){}

	/**
 * Adds a route to the router that only match if the HTTP method is PATCH
 *
 * @param string $arg1
 * @param string\array $arg2
 * @param string/a_rray $arg3
 * @return Phalcon\Mvc\Router\Route
 */
	private function Method8($arg1 ,$arg2 ,interface1 $arg3 = NULL ,$arg4 = "asdgh",$arg5 = false){}
	private function Method11($arg1 ,$arg2 ,interface1 $arg3 = NULL ,$arg4 = "asdgh", $arg5 = array(1,2,3,array('asd'=>"1",'asdf'=>9)),$arg6 = 0){}
	public function setProperty4($property1){}
	public $pro_perty1;
	public static $property2 = 1;

	protected $property3;
	//protected $property4 = 1;

	private $property5 = array(1,2,3,"4");
	private $property6 = 1;
	const BACD = 1;
	const BACD1 = 0.5;
	const BACD2 = false;
	const BACD3 = 'false';
	const BACD4 = NULL;
}