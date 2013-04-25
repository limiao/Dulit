<?php
include_once("define.php");
include("demo.php");
$project = new Extension("lemo");
$project->setFullName('Dulit FrameWork')
		->setAuthor('limiao');
$project->addClass("Dulit\\Core\\interface1");
$project->addClass("Dulit\\Core\\interface2");
$project->addClass("Dulit\\Core\\ParentClass");
$project->addClass("Dulit\\Core\\Topclass");
$project->addClass("Dulit\\Core\\Application");
$project->createProject();

function str_format($str) {
	$args = func_get_args();
	$count = count($args);

	if ($count > 1) {
		for ($i = 1; $i < $count; $i++) {
			$s = '{'.($i-1).'}';
			$str = str_replace($s, $args[$i], $str);
		}
	}
	return $str;
}

function template_format($str, array $arr) {
	if(count($arr)>0) {
		$pattern = '/\{#([\s\S]+?)#\}/im';
		preg_match_all($pattern, $str, $matches);

		foreach($arr as $key=>$value) {
			$$key = $value;
		}

		for($i = 0; $i<count($matches[0]); $i++) {
			echo $matches[1][$i];
			eval('$s = '.$matches[1][$i].';'); 
			$str = str_replace($matches[0][$i], $s, $str);
		}
	}
	
	return $str;
}

function array_compare(array $arr1, array $arr2) {
	if(count($arr1) != count($arr2)) return false;

	foreach($arr1 as $key=>$val) {
		if(!isset($arr2[$key]) 
			|| gettype($val) != gettype($arr2[$key])
			|| (gettype($val) != 'array' && $val != $arr2[$key])
			|| (gettype($val) == 'array' && array_compare($val, $arr2[$key]))) {
			return false;
		}
	}
	return true;
}

final class Extension {
	private $project_name;
	private $path;
	private $class_list = array();
	private $author = 'dulit';
	private $full_name;
	private $copyright;
	private $varsion = '0.1';
	private $declaration;

	function  __construct($name) {
		$this->project_name = $name;
		$path = __DIR__.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		if(!file_exists($path))mkdir($path);
		$this->path = $path;
	}

	function getProjectName() {
		return $this->project_name;
	}

	function getProjectHeadFile() {
		return 'php_'.strtolower($this->project_name);
	}

	function setFullName($full_name) {
		$this->full_name = $full_name;
		return $this;
	}

	function setAuthor($author) {
		$this->author = $author;
		return $this;
	}
	function getAuthor() {
		return $this->author;
	}

	function setCopyright($copyright) {
		$this->copyright = $copyright;
		return $this;
	}

	function setDeclaration($declaration) {
		$this->declaration = $declaration;
		return $this;
	}

	private function createTable(array $rows) {
		if (count($rows) > 0) {
			$line_width = 70;
			$line = '+'.str_repeat('-',$line_width).'+';
			$table = $line;

			foreach ($rows as $row) {
				if (strlen($row) > 0) {
					$words = preg_split('/\s+/i', $row);
					$temp_sentence = '|';
					$sentence = '';

					foreach ($words as $word) {
						if (strlen($temp_sentence) + strlen($word) < $line_width) {
							$temp_sentence .= ' '.$word;
						}
						else {
							$sentence .= $temp_sentence.str_repeat(' ',$line_width - strlen($temp_sentence) + 1).'|'.CR;
							$temp_sentence = '| '.$word;
						}					
					}
					$sentence .= CR.$temp_sentence.str_repeat(' ',$line_width - strlen($temp_sentence) + 1).'|'.CR;
					$table .= $sentence.$line;
				}
			}
			return $table;
		}
		return NULL;
	}

	function getStatement() {
		$statement = array('full_name'=>$this->full_name, 
			'copyright'=>$this->copyright, 
			'declaration'=>$this->declaration, 
			'author'=>'Author: ' .$this->author);
		$result = $this->createTable($statement);

		return $result;
	}

	function addClass($className) {
		$class = new ExtClass($className,$this);
		$class->create();
		$this->_class_list[$className] = $class;
	}

	function createFile($file_name, $template, $data) {
		$template_str = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.$template.'.temp');
		$content = template_format($template_str, $data);
		file_put_contents($this->path.$file_name, $content);
		echo $content;
		return true;
	}
	
	function createProject() {
		$data = array();
		$data['app_name'] = $this->project_name;
		$data['statement'] = $this->getStatement();
		$data['author'] = $this->getAuthor();
		$data['varsion'] = $this->varsion;
		$data['full_name'] = $this->full_name;
		
		foreach($this->_class_list as $class) {
			$data['classes'][] = $class->getClassNameL();
		}
		$this->createFile($this->project_name.'.c', 'application.c', $data);
		$this->createFile('php_'.$this->project_name.'.h', 'php_application.h', $data);
		$this->createFile('credits', 'CREDITS', $data);
		$this->createFile('experimental', 'EXPERIMENTAL', $data);
	}
}

final class ExtClass {
	private $project;
	private $ref_obj;
	private $class_name;
	private $class_name_l;
	private $class_name_str;
	private $class_entry_ce;
	private $method_entry_ce;
	private $include_file_app = array();
	private $type = array('integer'=>'long','double'=>'double','boolean'=>'bool','string'=>'string','NULL'=>'zval','array'=>'zval');

	function __construct($class_name, Extension $project) {
		$this->project = $project;
		$this->class_name = $class_name;
		$this->class_name_l = strtolower(str_replace('\\','_',$class_name));
		$this->class_name_str = addslashes($class_name);
		$this->class_entry_ce = str_format(ZEND_CLASS_ENTRY_CE_NAME, $this->class_name_l);
		$this->method_entry_ce = str_format(ZEND_FUNCTION_ENTRY_CE_NAME, $this->class_name_l);
		$project_head_file = $this->project->getProjectHeadFile();
		$this->include_file_app[$project_head_file] = $project_head_file.'.h';
		$this->include_file_app[$this->class_name_l] = $this->class_name_l.'.h';
	}

	public function getClassName() {
		return $this->class_name;
	}

	public function getClassNameL() {
		return $this->class_name_l;
	}

	public function create(){
		$data = array();
		$data['class_name'] = $this->class_name_l;
		$data['class_entry_ce'] = $this->class_entry_ce;
		$data['statement'] = $this->project->getStatement();
		$data['author'] = $this->project->getAuthor();
		$data['php_methods'] = array();
		$this->ref_obj = $this->parseObj($this->class_name);

		if(isset($this->ref_obj['methods']) && count($this->ref_obj['methods']) > 0) {
			$method_define = $this->createMethodDefine($this->ref_obj['methods']);
			$data['method_define'] = $method_define;
			$arg_info = $this->createArgInfo($this->ref_obj['methods']);
			$data['arg_info'] = $arg_info;
			$function_entry = $this->createFunctionEntry($this->ref_obj['methods'], $this->ref_obj['info']['type']);
			$data['function_entry'] = $function_entry;

			foreach($this->ref_obj['methods'] as $method_name=>$method) {
				if($this->ref_obj['info']['type']=='class' && !isset($method['acc']['abstract'])) {
					$data['php_methods'][] = $this->createMethod($method_name, $method);
				}
			}
		}

		$minit_function = $this->createMinitFunction($this->ref_obj);
		$data['minit_function'] = $minit_function;
		$data['include_file'] = $this->include_file_app;
		$this->project->createFile($this->class_name_l.'.h', 'class.h', $data);
		$this->project->createFile($this->class_name_l.'.c','class.c',$data);
	}

	private function getObjceName($obj_name) {
		return str_format(ZEND_CLASS_ENTRY_CE_NAME, $this->class_name_l);
	}
	
	private function parseVal($value) {
		$type = gettype($value);

		if($type == 'boolean'){
			$value = $value ? "1" : "0";
		}
		elseif($type == 'string'){
			$value = '"'.$value.'"';
		}

		return $value;
	}

	private function createArray($arrName, array $arr, $level = 0) {
		$tab = str_repeat(TAB, $level);
		$result = $tab.str_format(MAKE_STD_ZVAL, $arrName).CR;
		$result .= $tab.str_format(ARRAY_INIT, $arrName).CR;
		$prev_key = null;

		foreach($arr as $key=>$val) {
			$val_type = gettype($val);
			$key_type = gettype($key);

			if($val_type == 'array') {
				$subArrName = $arrName.'_'.$key;
				$result .= $tab."zval *$subArrName;".CR;
				$sub = $this->createArray($subArrName, $val, $level);
				$result .= $sub;
				$val = $subArrName;
			}
			elseif($val_type == "string") {
				$val = '"'.$val.'", 1';
			}

			if ($key_type == 'integer') {
				if($prev_key == $key - 1) {
					$result .= $tab.str_format(ADD_NEXT_INDEX, $this->type[$val_type], $arrName, $val).CR;
				}
				else {
					$result .= $tab.str_format(ADD_INDEX, $this->type[$val_type], $arrName, $key, $val).CR;
				}
			}
			else {
				$result .= $tab.str_format(ADD_ASSOC, $this->type[$val_type], $arrName, $key, $val).CR;
			}

			$prev_key = $key;
		}

		return $result;
	}

	private function includeObj($obj_name) {
		$obj_name_l = strtolower(str_replace('\\', '_', $obj_name));
		$this->include_file_app[$obj_name_l] = $obj_name_l.'.h';
		$obj_name_ce = str_format(ZEND_CLASS_ENTRY_CE_NAME, $obj_name_l);
		return $obj_name_ce;
	}

	private function parseObj($class_name) {
		$ref_arr = array();
		$class = new ReflectionClass($class_name);
		$parent_class = $class->getParentClass();
		$constants = $class->getConstants();
		$methods = $class->getMethods();
		$doc_return_pattern = '/(?<=@return)\s+(\S+)(?=\b)/im';
		$doc_param_pattern = '/(?<=@param)\s+(\S+)\s+\$(\w+)(?=\b)/im';
		
		if($class->isInterface()){
			$ref_arr['info']['type'] = 'interface';
		}
		else{
			$ref_arr['info']['type'] = 'class';
			$interface = $class->getInterfaces();
			$properties = $class->getDefaultProperties();

			if($parent_class) {
				$ref_arr['info']['parent'] = $parent_class->getName();
			}
			if($class->isFinal()) {
				$ref_arr['info']['acc'] = 'final';
			}
			elseif($class->isAbstract()) {
				$ref_arr['info']['acc'] = 'abstract';
			}

			if(count($interface) > 0){
				if($parent_class){
					$parent_class_interface = $parent_class->getInterfaces();

					if(count($parent_class_interface) > 0) {
						foreach($interface as $key=>$val) {
							if(array_key_exists($key, $parent_class_interface)) {
								unset($interface[$key]);
							}
						}
					}
				}

				if(count($interface) > 0){
					foreach($interface as $key=>$val) {
						$ref_arr['info']['interfaces'][] = $key;
					}
				}
			}

			foreach($properties as $name=>$value) {
				$property = $class->getProperty($name);
				if($property->getDeclaringClass()->getName() != $class->getName())continue;
				$zend_acc = array();

				if($property->isPrivate()) $zend_acc[] = "ZEND_ACC_PRIVATE";
				if($property->isProtected()) $zend_acc[] = "ZEND_ACC_PROTECTED";
				if($property->isPublic()) $zend_acc[] = "ZEND_ACC_PUBLIC";
				if($property->isStatic()) $zend_acc[] = "ZEND_ACC_STATIC";

				$ref_arr['properties'][$name]['acc'] = $zend_acc;
				$ref_arr['properties'][$name]['value'] = $value;
				$ref_arr['properties'][$name]['type'] = gettype($value);
			}
		}

		foreach($constants as $name => $value){
			if ($parent_class && $parent_class->hasConstant($name))continue;
			$ref_arr['constants'][$name]['value'] = $value;
			$ref_arr['constants'][$name]['type'] = gettype($value);
		}
		
		foreach($methods as $method) {
			if($method->getDeclaringClass()->getName()!=$class->getName()) continue;

			$method_name = $method->getName();
			$doc_comment = $method->getDocComment();
			$params = $method->getParameters();
			$zend_acc = array();

			if($method->isPrivate()) $zend_acc['private'] = "ZEND_ACC_PRIVATE";
			if($method->isProtected()) $zend_acc['protected'] = "ZEND_ACC_PROTECTED";
			if($method->isPublic()) $zend_acc['public'] = "ZEND_ACC_PUBLIC";
			if($method->isFinal()) $zend_acc['final'] = "ZEND_ACC_FINAL";
			if($method->isStatic()) $zend_acc['static'] = "ZEND_ACC_STATIC";
			if($method->isAbstract()) $zend_acc['abstract'] = "ZEND_ACC_ABSTRACT";
			if($method->isConstructor()) $zend_acc['constructor'] = "ZEND_ACC_CTOR";
			if($method->isDestructor()) $zend_acc['destructor'] = "ZEND_ACC_DTOR";
			$ref_arr['methods'][$method_name]['acc'] = $zend_acc;
			
			if(!empty($doc_comment)){
				preg_match_all($doc_param_pattern, $doc_comment, $param_matches);
				if(count($param_matches[2]) > 0) {
					$params_doc = array();
					$i = 0;
					foreach($param_matches[2] as $doc_arg) {
						$params_doc[$doc_arg] = strtolower($param_matches[1][$i++]);
					}
				}
				if (preg_match($doc_return_pattern, $doc_comment, $return_matches)) {
					$ref_arr['methods'][$method_name]['return'] = $return_matches[1];
				}
				$ref_arr['methods'][$method_name]['doc'] = $doc_comment;
			}
			else {
				unset($params_doc);
			}

			if(count($params) > 0) {
				foreach($params as $param) {
					$param_arr = array();
					$param_name = $param->getName();
					$param_class = $param->getClass();
					$param_arr['name'] = $param_name;
					$param_arr['isPassedByReference'] = $param->isPassedByReference() ? '1' : '0';
					$param_arr['isOptional'] = $param->isOptional() ? '1' : '0';
					$param_arr['isArray'] = '0';
					$param_arr['isClass'] = '0';
					$param_arr['isClass'] = '0';

					if(isset($params_doc) && array_key_exists($param_name, $params_doc) && !isset($param_class) && !$param->isArray()) {
						$param_arr['type'] = $params_doc[$param_name];
					}
					elseif(isset($param_class)) {
						$param_arr['type'] = $param_class->getName();
						$param_arr['isClass'] = '1';
					}
					elseif($param->isArray()) {
						$param_arr['type'] = 'array';
						$param_arr['isArray'] = '1';
					}
					
					if($param->isDefaultValueAvailable()) {
						$param_arr['default'] = $param->getDefaultValue();
					}

					$ref_arr['methods'][$method_name]['params'][] = $param_arr;
				}
			}
		}

		return $ref_arr;
	}

	private function findProperty($name) { 
		if(isset($this->ref_obj['properties']) && count($this->ref_obj['properties']) > 0){
			foreach($this->ref_obj['properties'] as $property_name=>$property) {
				if(strtolower(str_replace('_','',$property_name)) == strtolower(str_replace('_','',$name))) {
					return $property_name;
				}
			}
		}
		return false;
	}

	private function createMinitFunction($obj){
		$minit_function = str_format(ZEND_MINIT_FUNCTION, $this->class_name_l).'{'.CR;
		$minit_function .= TAB.ZEND_FUNCTION_ENTRY.' ce;'.CR;
		$minit_function .= TAB.str_format(INIT_CLASS_ENTRY, 'ce', $this->class_name_str, $this->method_entry_ce).CR;

		if($obj['info']['type'] == 'class') {
			if(isset($obj['info']['parent'])) {
				$parent_class_name = addslashes($obj['info']['parent']);
				$parent_class_name_ce = $this->includeObj($obj['info']['parent']); 
				$minit_function .= TAB.str_format(ZEND_REGISTER_INTERNAL_CLASS_EX, $this->class_entry_ce, 'ce', $parent_class_name_ce, $parent_class_name).CR;
			}
			else {
				$minit_function .= TAB.str_format(ZEND_REGISTER_INTERNAL_CLASS, $this->class_entry_ce, 'ce').CR;
			}

			if(isset($obj['info']['interfaces']) && count($obj['info']['interfaces']) > 0) {
				foreach($obj['info']['interfaces'] as $interface) {
					$interfaces[] = $this->includeObj($interface); 
				}
				$minit_function .= TAB.str_format(ZEND_CLASS_IMPLEMENTS, $this->class_entry_ce, count($interfaces), join(', ',$interfaces)).CR;
			}

			if(isset($obj['info']['acc']) && $obj['info']['acc'] == 'final') {
				$minit_function .= TAB.str_format(ZEND_ACC_FINAL_CLASS, $this->class_entry_ce).CR;
			}
			elseif(isset($obj['info']['acc']) && $obj['info']['acc'] == 'abstract') {
				$minit_function .= TAB.str_format(ZEND_ACC_ABSTRACT_CLASS, $this->class_entry_ce).CR;
			}
		}
		else {
			$minit_function .= TAB.str_format(ZEND_REGISTER_INTERNAL_INTERFACE, $this->class_entry_ce, 'ce').CR;
		}

		if (isset($obj['constants']) && count($obj['constants']) > 0) {
			foreach($obj['constants'] as $constant_name=>$constant)
			{
				$value = $this->parseVal($constant['value']);
				$type = $constant['type'];
				if($type == 'NULL') {
					$minit_function .= TAB.str_format(ZEND_DECLARE_CLASS_CONSTANT_NULL, $this->class_entry_ce, $constant_name).CR;
				}
				else {
					$minit_function .= TAB.str_format(ZEND_DECLARE_CLASS_CONSTANT, $this->type[$type], $this->class_entry_ce, $constant_name, $value).CR;
				}
			}
		}

		if (isset($obj['properties']) && count($obj['properties']) > 0) {
			foreach($obj['properties'] as $property_name=>$property) {
				$value = $this->parseVal($property['value']);
				$zend_acc = join('|' ,$property['acc']);
				$type = $property['type'];
				if($type == 'NULL') {
					$minit_function .= TAB.str_format(ZEND_DECLARE_PROPERTY_NULL, $this->class_entry_ce, $property_name, $zend_acc).CR;
				}
				elseif($type == 'array') {
					$minit_function .= TAB."zval *$property_name;".CR.$this->createArray($property_name, $value, 1);
					$minit_function .= TAB.str_format(ZEND_DECLARE_PROPERTY_ZVAL, $this->class_entry_ce, $property_name, $zend_acc).CR;
				}
				else {
					$minit_function .= TAB.str_format(ZEND_DECLARE_PROPERTY, $this->type[$type], $this->class_entry_ce, $property_name, $value, $zend_acc).CR;
				}
			}
		}

		$minit_function .= TAB.'return SUCCESS;'.CR.'}';
		return $minit_function;
	}

	private function createArgInfo($methods) {
		$zend_arg_info = array();

		foreach($methods as $method_name=>$method) {
			if(isset($method['params']) && count($method['params']) > 0) {
				$arg_info_params = array();
				$required_num_args = 0;
				foreach($method['params'] as $param) {
					if($param['isOptional'] == '0') {
						$required_num_args++;
					}
					if($param['isArray'] == '1') {
						$arg_info_params[] = TAB.str_format(ZEND_ARG_ARRAY_INFO, $param['isPassedByReference'], $param['name'], $param['isOptional']);
					}
					elseif($param['isClass'] == '1') {
						$this->includeObj($param['type']); 
						$arg_info_params[] = TAB.str_format(ZEND_ARG_OBJECT_INFO, 1, $param['name'], addslashes($param['type']), $param['isOptional']);
					}
					else {
						$arg_info_params[] = TAB.str_format(ZEND_ARG_INFO, $param['isPassedByReference'], $param['name']);
					}
				}

				$arg_info_name = str_format(ZEND_ARG_INFO_NAME, $this->class_name_l, strtolower($method_name));
				$zend_arg_info[] = str_format(ZEND_BEGIN_ARG_INFO_EX, $arg_info_name, 0, 0, $required_num_args).CR.join(CR, $arg_info_params).CR.ZEND_END_ARG_INFO;
			}
		}

		return join(CR.CR, $zend_arg_info);
	}

	private function createFunctionEntry($methods, $type) {
		$function_entry = ZEND_FUNCTION_ENTRY.' '.$this->method_entry_ce.'[] = {' .CR;

		foreach($methods as $method_name=>$method) {
			if(isset($method['params']) && count($method['params']) > 0) {
				$arg_info_name = str_format(ZEND_ARG_INFO_NAME, $this->class_name_l, strtolower($method_name));
			}
			else {
				$arg_info_name = 'NULL';
			}
			if($type == 'class') {
				$function_entry .= TAB.str_format(PHP_ME, $this->class_name_l, $method_name, $arg_info_name, join('|' ,$method['acc'])).CR;
			}
			elseif($type == 'interface') {
				$function_entry .= TAB.str_format(PHP_ABSTRACT_ME, $this->class_name_l, $method_name, $arg_info_name).CR;
			}
		}

		$function_entry .= TAB.PHP_FE_END.CR;
		$function_entry .= '}';

		return $function_entry;
	}

	private function createMethodDefine($methods) {
		$method_defines = array();
		foreach($methods as $method_name=>$method) {
			$method_defines[] = str_format(PHP_METHOD, $this->class_name_l, $method_name).';';
		}

		return join(CR, $method_defines);
	}

	private function createMethod($method_name, $method) {
		$get_param_design = ''; $php_method_designs = ''; $set_return_designs = '';$var_designs = '';
		$var_char = array(); $var_long = array(); $var_bool = array(); $var_double = array(); $var_int = array(); $var_zval = array();

		if(isset($method['params']) && count($method['params']) > 0) {
			$type_spec_has_default_val = '';$type_spec_no_default_val = '';
			$var_param_arr = array();
			$param_init_arr = array();
			foreach($method['params'] as $param) {
				$var_param_arr[] = '&'.$param['name'];
				if(isset($param['default'])) {
					if($param['isArray'] == '1' || (isset($param['type']) && $param['type'] == 'array')) {
						$var_zval[] = '*'.$param['name'].' = NULL';
						$type_spec_has_default_val .= 'a';

						if ($param['default'] != NULL) {
							$param_init_arr[] = TAB.'if (!'.$param['name'].') {'.CR.$this->createArray($param['name'], $param['default'], 2).TAB."}".CR.CR;
						}
					}
					elseif($param['isClass'] == '1') {
						$var_zval[] = '*'.$param['name'].' = NULL';
						$type_spec_has_default_val .= 'O';
					}
					elseif(isset($param['type']) && $param['type'] == 'string') {
						$var_param_arr[] = '&'.$param['name'].'_len';
						$var_char[] = '*'.$param['name'].' = "'.addslashes($param['default']).'"';
						$var_int[] = $param['name'].'_len = '.(strlen($param['default']) - 1);
						$type_spec_has_default_val .= 's';
					}
					elseif(isset($param['type']) && ($param['type'] == 'integer' || $param['type'] == 'int' || $param['type'] == 'long')) {
						$var_long[] = $param['name'].' = '.$param['default'];
						$type_spec_has_default_val .= 'l';
					}
					elseif(isset($param['type']) && ($param['type'] == 'boolean' || $param['type'] == 'bool')) {
						$var_bool[] = $param['name'].' = '.($param['default']==true ? "1" : "0");
						$type_spec_has_default_val .= 'b';
					}
					elseif(isset($param['type']) && ($param['type'] == 'double' || $param['type'] == 'float')) {
						$var_double[] = $param['name'].' = '.$param['default'];
						$type_spec_has_default_val .= 'd';
					}
					else {
						$var_zval[] = '*'.$param['name'].' = NULL';
						$type_spec_has_default_val .= 'z';
						$type = gettype($param['default']);
						$param_init_str = TAB.TAB.str_format(MAKE_STD_ZVAL, $param['name']).CR;

						if ($type == 'integer') {
							$param_init_str .= TAB.TAB.str_format(ZVAL_LONG, $param['name'], $param['default']).CR;
						}
						elseif ($type == 'boolean') {
							$param_init_str .= TAB.TAB.str_format(ZVAL_BOOL, $param['name'], $param['default']? '1' : '0').CR;
						}
						elseif ($type == 'double') {
							$param_init_str .= TAB.TAB.str_format(ZVAL_DOUBLE, $param['name'], $param['default']).CR;
						}
						elseif ($type == 'string') {
							$param_init_str .= TAB.TAB.str_format(ZVAL_STRINGL, $param['name'], $param['default'], strlen($param['default'])).CR;
						}
						elseif ($type == 'array') {
							$param_init_str = $this->createArray($param['name'], $param['default'], 2);
						}
						elseif ($type == 'NULL') {
							$param_init_str .= TAB.TAB.str_format(ZVAL_NULL, $param['name']).CR;
						}

						$param_init_arr[] = TAB.'if (!'.$param['name'].') {'.CR.$param_init_str.TAB."}".CR.CR;
					}
				}
				else {
					if($param['isArray'] == '1' || (isset($param['type']) && $param['type'] == 'array')) {
						$var_zval[] = '*'.$param['name'];
						$type_spec_no_default_val .= 'a';
					}
					elseif($param['isClass'] == '1') {
						$var_zval[] = '*'.$param['name'];
						$type_spec_no_default_val .= 'O';
					}
					elseif(isset($param['type']) && ($param['type'] == 'string')) {
						$var_param_arr[] = '&'.$param['name'].'_len';
						$var_char[] = '*'.$param['name'];
						$var_int[] = $param['name'].'_len';
						$type_spec_no_default_val .= 's';
					}
					elseif(isset($param['type']) && ($param['type'] == 'integer' || $param['type'] == 'int' || $param['type'] == 'long')) {
						$var_long[] = $param['name'];
						$type_spec_no_default_val .= 'l';
					}
					elseif(isset($param['type']) && ($param['type'] == 'boolean' || $param['type'] == 'bool')) {
						$var_bool[] = $param['name'];
						$type_spec_no_default_val .= 'b';
					}
					elseif(isset($param['type']) && ($param['type'] == 'double' || $param['type'] == 'float')) {
						$var_double[] = $param['name'];
						$type_spec_no_default_val .= 'd';
					}
					else {
						$var_zval[] = '*'.$param['name'];
						$type_spec_no_default_val .= 'z';
					}
				}
			}

			$type_spec = $type_spec_no_default_val.(!empty($type_spec_has_default_val)? "|{$type_spec_has_default_val}":'');
			$get_param_design = TAB.'if ('.str_format(ZEND_PARSE_PARAMETERS, $type_spec, join(', ', $var_param_arr)).') {'.CR.TAB.TAB.RETURN_NULL.CR.TAB.'}'.CR.CR.join('',$param_init_arr);
		}

		if (strtolower(substr($method_name,0,3)) == 'set' && isset($method['params']) && count($method['params']) == 1) {
			//if ($this->findProperty(substr($method_name,3)))
				//$php_method_designs .=TAB.'update_'.CR;
			//else
				//$php_method_designs .=TAB.'add_'.CR;
		}
		
		$php_method_designs .= TAB."php_printf(\"call {$method_name}\");".CR;

		if(isset($method['return'])) {
			if (strtolower(substr($method_name,0,3)) == 'get' && $this->findProperty(substr($method_name,3))) {
				//$php_method_designs .=TAB.'read_'.CR;
			}

			if($method['return'] == 'string') {
				$set_return_designs = TAB.str_format(RETURN_STRINGL, 'success','7').CR;
			}
			elseif($method['return'] == 'integer' || $method['return'] == 'int' || $method['return'] == 'long') {
				$set_return_designs = TAB.str_format(RETURN_LONG, '0').CR;
			}
			elseif($method['return'] == 'boolean' || $method['return'] == 'bool') {
				$set_return_designs = TAB.str_format(RETURN_BOOL, '0').CR;
			}
			elseif($method['return'] == 'double' || $method['return'] == 'float') {
				$set_return_designs = TAB.str_format(RETURN_DOUBLE, '0.0').CR;
			}
			elseif($method['return'] == 'void' || $method['return'] == 'NULL') {
				$set_return_designs = TAB.str_format(RETURN_NULL).CR;
			}
			elseif(class_exists($method['return'], false))
				$set_return_designs = TAB.$method['return'].CR;
		}
		
		if(count($var_char)>0){$var_designs .= TAB.'char '.join(', ',$var_char).';'.CR;}
		if(count($var_long)>0){$var_designs .= TAB.'long '.join(', ',$var_long).';'.CR;}
		if(count($var_bool)>0){$var_designs .= TAB.'zend_bool '.join(', ',$var_bool).';'.CR;}
		if(count($var_double)>0){$var_designs .= TAB.'double '.join(', ',$var_double).';'.CR;}
		if(count($var_int)>0){$var_designs .= TAB.'int '.join(', ',$var_int).';'.CR;}
		if(count($var_zval)>0){$var_designs .= TAB.'zval '.join(', ',$var_zval).';'.CR;}
		$php_method_designs = str_format(PHP_METHOD, $this->class_name_l, $method_name).'{'.CR.$var_designs.CR.$get_param_design.$php_method_designs.$set_return_designs.'}';

		return $php_method_designs;
	}
}