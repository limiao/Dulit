<?php
define('CR', "\r\n");
define('TAB', "\t");

//方法注册相关
define('ZEND_FUNCTION_ENTRY', 'zend_function_entry');
define('ZEND_FUNCTION_ENTRY_CE_NAME', '{0}_methods');
define('PHP_ME',  'PHP_ME({0}, {1}, {2}, {3})');
define('PHP_ABSTRACT_ME',  'PHP_ABSTRACT_ME({0}, {1}, {2})');
define('PHP_FE_END', '{NULL, NULL, NULL}'); 
define('PHP_METHOD', 'PHP_METHOD({0}, {1})');

//对象注册相关
define('ZEND_CLASS_ENTRY', 'zend_class_entry');
define('ZEND_CLASS_ENTRY_CE_NAME', '{0}_ce');
define('ZEND_MINIT_FUNCTION', 'ZEND_MINIT_FUNCTION({0})');
define('INIT_CLASS_ENTRY', 'INIT_CLASS_ENTRY({0}, "{1}", {2});');
define('ZEND_REGISTER_INTERNAL_INTERFACE','{0} = zend_register_internal_interface(&{1} TSRMLS_CC);');
define('ZEND_REGISTER_INTERNAL_CLASS','{0} = zend_register_internal_class(&{1} TSRMLS_CC);');
define('ZEND_REGISTER_INTERNAL_CLASS_EX','{0} = zend_register_internal_class_ex(&{1}, {2}, "{3}" TSRMLS_CC);');
define('ZEND_CLASS_IMPLEMENTS','zend_class_implements({0} TSRMLS_CC, {1}, {2});');
define('ZEND_ACC_FINAL_CLASS','{0}->ce_flags |= ZEND_ACC_FINAL_CLASS;');
define('ZEND_ACC_ABSTRACT_CLASS','{0}->ce_flags |= ZEND_ACC_ABSTRACT_CLASS;');
define('ZEND_MODULE_STARTUP_N', 'ZEND_MODULE_STARTUP_N({0})(INIT_FUNC_ARGS_PASSTHRU);');

//获取参数
define('ZEND_PARSE_PARAMETERS', 'zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "{0}", {1})');

//参数类型限定定义
define('ZEND_ARG_INFO_NAME', 'arg_{0}_{1}');
define('ZEND_BEGIN_ARG_INFO_EX', 'ZEND_BEGIN_ARG_INFO_EX({0}, {1}, {2}, {3})');
define('ZEND_ARG_INFO', 'ZEND_ARG_INFO({0}, {1})');
define('ZEND_ARG_OBJECT_INFO', 'ZEND_ARG_OBJECT_INFO({0}, {1}, "{2}", {3})');
define('ZEND_ARG_ARRAY_INFO', 'ZEND_ARG_ARRAY_INFO({0}, {1}, {2})');
define('ZEND_END_ARG_INFO', 'ZEND_END_ARG_INFO()');

//常量定义
define('ZEND_DECLARE_CLASS_CONSTANT', 'zend_declare_class_constant_{0}({1}, ZEND_STRL("{2}"), {3} TSRMLS_DC);');
define('ZEND_DECLARE_CLASS_CONSTANT_NULL', 'zend_declare_class_constant_null({0}, ZEND_STRL("{1}") TSRMLS_DC);');

//属性定义
define('ZEND_DECLARE_PROPERTY', 'zend_declare_property_{0}({1}, ZEND_STRL("{2}"), {3}, {4} TSRMLS_DC);');
define('ZEND_DECLARE_PROPERTY_ZVAL', 'zend_declare_property({0}, ZEND_STRL("{1}"), {1}, {2} TSRMLS_DC);');
define('ZEND_DECLARE_PROPERTY_NULL', 'zend_declare_property_null({0}, ZEND_STRL("{1}"), {2} TSRMLS_DC);');

//数组操作函数
define('ARRAY_INIT', 'array_init({0});');
define('ADD_NEXT_INDEX', 'add_next_index_{0}({1}, {2});');
define('ADD_INDEX', 'add_index_{0}({1}, {2}, {3});');
define('ADD_ASSOC', 'add_assoc_{0}({1}, "{2}", {3});');

//PHP变量类型
define('MAKE_STD_ZVAL', 'MAKE_STD_ZVAL({0});');
define('ZVAL_NULL', 'ZVAL_NULL({0});');
define('ZVAL_BOOL', 'ZVAL_BOOL({0}, {1});');
define('ZVAL_LONG', 'ZVAL_LONG({0}, {1});');
define('ZVAL_DOUBLE', 'ZVAL_DOUBLE({0}, {1});');
define('ZVAL_STRINGL', 'ZVAL_STRINGL({0}, {1}, {2}, 1);');

//返回数据定义
define('RETURN_BOOL', 'RETURN_BOOL({0});');
define('RETURN_NULL', 'RETURN_NULL();');
define('RETURN_LONG', 'RETURN_LONG({0});');
define('RETURN_DOUBLE', 'RETURN_DOUBLE({0}, {1});');
define('RETURN_STRING', 'RETURN_STRING("{0}", 1);');
define('RETURN_STRINGL', 'RETURN_STRINGL("{0}", {1}, 0);');
define('RETURN_ZVAL', 'RETURN_ZVAL({0}, 1, 0);');
