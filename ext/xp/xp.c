/*
  +----------------------------------------------------------------------+
  | PHP Version 4                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2002 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available at through the world-wide-web at                           |
  | http://www.php.net/license/2_02.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:     Timm Friebe <xp@thekid.de>                               |
  +----------------------------------------------------------------------+

  $Id$ 
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_xp.h"
#include "ext/standard/php_var.h"
#include "ext/standard/php_smart_str.h"

#define XP_CTOR  "__construct"

#define XP_DEBUG 1

ZEND_DECLARE_MODULE_GLOBALS(xp)

/* True global resources - no need for thread safety here */
/* static int le_xp; */

/* {{{ xp_functions[]
 *
 */
function_entry xp_functions[] = {
	PHP_FE(uses,	NULL)
	PHP_FE(cast,	first_arg_force_ref)
	PHP_FE(try,		NULL)
	PHP_FE(catch,	second_arg_force_ref)
	PHP_FE(throw,	first_arg_force_ref)
	PHP_FE(finally,	NULL)
	PHP_FE(__name,	first_arg_force_ref)
	{ NULL, NULL, NULL }
};
/* }}} */

/* {{{ xp_class_methods[]
 *
 */
static function_entry xp_class_methods[] = {
	PHP_FE(__construct,	NULL)
	PHP_FE(getclass,	NULL)
	PHP_FE(tostring,	NULL)
	{ NULL, NULL, NULL }
};
/* }}} */

/* {{{ xp_module_entry
 */
zend_module_entry xp_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"xp",
	xp_functions,
	PHP_MINIT(xp),
	PHP_MSHUTDOWN(xp),
	PHP_RINIT(xp),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(xp),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(xp),
#if ZEND_MODULE_API_NO >= 20010901
	NO_VERSION_YET,
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

zend_class_entry xp_object_class_entry;

static php_xp_classlist xp_core_class_names[] = {
};

#ifdef COMPILE_DL_XP
ZEND_GET_MODULE(xp)
#endif

/* {{{ PHP_INI
 */
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("xp.class_path", "/usr/home/thekid/devel/rmnt/skeleton", PHP_INI_ALL, OnUpdateString, class_path, zend_xp_globals, xp_globals)
PHP_INI_END()
/* }}} */

#if 0
static int print_values(zval **zv, int num_args, va_list args, zend_hash_key *hash_key)
{
	fprintf(stderr, "--- string(%d)'%s' => ", hash_key->nKeyLength, hash_key->arKey);
	zend_print_zval_r(*zv, 0);
	fprintf(stderr, "\n");
	return 0;
}
#endif

static char *php_xp_get_classname(char *name, int name_len) 
{
	zval **class_name= NULL;
	TSRMLS_FETCH(); 
	
	if (FAILURE == zend_hash_find(&XPG(names), name, name_len, (void **)&class_name)) {
		char *dst;
		
		dst = (char*) emalloc(name_len + sizeof("php."));
		strcpy(dst, "php.");
		strcat(dst, name);
		return dst;
	}

	return estrndup(Z_STRVAL_PP(class_name), Z_STRLEN_PP(class_name));
}


/* {{{ php_xp_call
 */
void php_xp_call(INTERNAL_FUNCTION_PARAMETERS, zend_property_reference *property_reference)
{
	zval *object = property_reference->object;
	zval method;
	zend_class_entry temp_ce, *orig_ce;
	zval ***args;
	zval *retval = NULL;
	zend_overloaded_element *overloaded_property = (zend_overloaded_element *)property_reference->elements_list->tail->data;
    TSRMLS_FETCH(); 
	
	/* Sanity checks */
	if ((OE_IS_METHOD != Z_TYPE_P(overloaded_property)) || (IS_STRING != Z_TYPE(overloaded_property->element))) {
		zval_dtor(&overloaded_property->element);
		return;
	}

	/* This is kind of like skipping the rest of the code - no class method calls will be executed */
	if (XPG(exception)->object) {
		#ifdef XP_DEBUG
		fprintf(
			stderr, 
			"xp>> skipping %s::%s() because of %s\n", 
			Z_OBJCE_P(object)->name, 
			Z_STRVAL(overloaded_property->element),
			Z_OBJCE_P(XPG(exception)->object)->name
		);
		#endif
		
		zval_dtor(&overloaded_property->element);
		return;
	}

	/* Fetch arguments */
	args = (zval ***)emalloc(ZEND_NUM_ARGS() * sizeof(zval **));
	if (zend_get_parameters_array_ex(ZEND_NUM_ARGS(), args) == FAILURE) {
		efree(args);
		zend_error(E_WARNING, "Unable to obtain arguments");
		return;
	}

	/* Disable call handler on this object */
	temp_ce = *Z_OBJCE_P(object);
	temp_ce.handle_function_call = NULL;
	orig_ce = Z_OBJCE_P(object);
	Z_OBJ_P(object)->ce = &temp_ce;

	/* What to call. If the method's name equals that of the object, call __construct */
	if (0 == strcasecmp(Z_OBJCE_P(object)->name, Z_STRVAL(overloaded_property->element))) {
		ZVAL_STRINGL(&method, XP_CTOR, sizeof(XP_CTOR)-1, 0);
	} else {
		ZVAL_STRINGL(&method, Z_STRVAL(overloaded_property->element), Z_STRLEN(overloaded_property->element), 0);
	}
	INIT_PZVAL(&method);

	/* Call function */
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> call %s::%s()\n", Z_OBJCE_P(object)->name, Z_STRVAL(method));
	#endif
	if (FAILURE == call_user_function_ex(NULL, &object, &method, &retval, ZEND_NUM_ARGS(), args, 0, NULL TSRMLS_CC)) {
		zend_error(E_WARNING, "Unable to call %s::%s\n", Z_OBJCE_P(object)->name, Z_STRVAL(method));
	}

	/* Clean up function's retval and copy it to return_value */
	if (retval) {
		*return_value = *retval;
		zval_copy_ctor(return_value);
		INIT_PZVAL(return_value);
		zval_ptr_dtor(&retval);
	}
		
	/* Restore call handler */
	Z_OBJ_P(object)->ce = orig_ce;	
	efree(args);
	zval_dtor(&overloaded_property->element);
}
/* }}} */

/* {{{ php_xp_init_globals
 */
static void php_xp_init_globals(zend_xp_globals *xp_globals TSRMLS_DC)
{
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> php_xp_init_globals\n");
	#endif
	zend_hash_init(&xp_globals->names, 10, NULL, ZVAL_PTR_DTOR, 1);
	xp_globals->class_path = NULL; /* estrndup(".", 1); */
}
/* }}} */

/* {{{ php_xp_destroy_globals
 */
static void php_xp_destroy_globals(zend_xp_globals *xp_globals TSRMLS_DC)
{
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> php_xp_destroy_globals\n");
	#endif
	zend_hash_destroy(&xp_globals->names);
}
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(xp)
{
	zend_class_entry *base_ce, ce;
	int i;
	TSRMLS_FETCH();
	
	ZEND_INIT_MODULE_GLOBALS(xp, php_xp_init_globals, php_xp_destroy_globals);
	REGISTER_INI_ENTRIES();

	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> PHP_MINIT_FUNCTION\n");
	#endif
		
	/* Register Object class */
	INIT_OVERLOADED_CLASS_ENTRY(xp_object_class_entry, "Object", xp_class_methods, php_xp_call, NULL, NULL);
	base_ce= zend_register_internal_class(&xp_object_class_entry TSRMLS_CC);
	
	/* Register other classes */
	for (i= 0; i < sizeof(xp_core_class_names) / sizeof (php_xp_classlist); i++) {
		fprintf(stderr, "xp>> INIT_CLASS_ENTRY %d: string(%d)'%s'\n", i, xp_core_class_names[i].name_len, xp_core_class_names[i].name);
		INIT_CLASS_ENTRY(ce, xp_core_class_names[i].name, NULL);
		ce.name_length= xp_core_class_names[i].name_len;
		zend_register_internal_class_ex(&ce, base_ce, NULL TSRMLS_CC);
	}
	
	/* Set error reporting to E_ALL */
	EG(error_reporting) = E_ALL;
	
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(xp)
{
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> PHP_MSHUTDOWN_FUNCTION\n");
	#endif

#ifdef ZTS
	ts_free_id(xp_globals_id);
#else
	php_xp_destroy_globals(&xp_globals TSRMLS_CC);
#endif

	UNREGISTER_INI_ENTRIES();

	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(xp)
{
	TSRMLS_FETCH(); 
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> PHP_RINIT_FUNCTION\n");
	#endif
	
	XPG(exception) = ecalloc(sizeof(php_xp_exception), 1);
	XPG(exception)->object = NULL;

	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(xp)
{
	TSRMLS_FETCH(); 
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> PHP_RSHUTDOWN_FUNCTION\n");
	#endif
	
	zend_hash_clean(&XPG(names));
	XP_CLEAR_EXCEPTION(XPG(exception));
	efree(XPG(exception));

	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(xp)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "XP Framework", "enabled");
	php_info_print_table_end();

	DISPLAY_INI_ENTRIES();
}
/* }}} */

/* {{{ php_xp_uses
 */
static int php_xp_uses(char *arg, int arg_len TSRMLS_DC)
{
	char *eval = NULL;
	char *desc;
	int i, ret, len;
	zval retval;
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> uses(string(%d)'%s')\n", arg_len, arg);
	#endif
	
	/* Build complete path to file */
	eval = (char *) emalloc(arg_len+ strlen(XPG(class_path))+ sizeof("include_once(' .class.php');"));
	sprintf(eval, "include_once('%s%c", XPG(class_path), DEFAULT_SLASH);
	len= strlen(eval);
	for (i = 0; i < arg_len; i++) {
		eval[i+ len]= ('.' == arg[i]) ? DEFAULT_SLASH : arg[i];
	}
	eval[i+ len]= '\0';
	strcat(eval, ".class.php');");

	/* Evaluate string */
	desc= zend_make_compiled_string_description("uses" TSRMLS_CC);	
	if (FAILURE == zend_eval_string(eval, &retval, desc TSRMLS_CC)) {
		efree(desc);
		zend_error(E_CORE_ERROR, "XP: Core error during execution of '%s'", eval);
		
		/* Bails out */
	}
	efree(desc);
	efree(eval);
	
	/* Where we successfull? */
	convert_to_boolean(&retval);
	ret= Z_BVAL(retval);
	zval_dtor(&retval);
	
	/* Store pretty-print name */
	if (ret) {
		zval *name;
		
		MAKE_STD_ZVAL(name);
		ZVAL_STRINGL(name, arg, arg_len, 1);
		
		arg= strrchr(arg, '.')+ 1;
		zend_str_tolower(arg, strlen(arg));
		zend_hash_update(&XPG(names), arg, strlen(arg), (void *)&name, sizeof(zval *), NULL);
	}
	
	return ret;
}
/* }}} */

/* {{{ proto bool uses(string* arg)
   Use a library */
PHP_FUNCTION(uses)
{
	zval ***args;
	int i, success;

	args = (zval ***)emalloc(ZEND_NUM_ARGS() * sizeof(zval **));
	if (zend_get_parameters_array_ex(ZEND_NUM_ARGS() TSRMLS_CC, args) == FAILURE) {
		efree(args);
		zend_error(E_WARNING, "XP: Unable to obtain arguments to uses");
		RETURN_FALSE;
	}
	
	/* Loop through all of the given arguments */
	success= 1;
	for (i = 0; i < ZEND_NUM_ARGS(); i++) {
		convert_to_string_ex(args[i]);
		success= success & php_xp_uses(Z_STRVAL_PP(args[i]), Z_STRLEN_PP(args[i]) TSRMLS_CC);
	}
	
	efree(args);
	if (success) {
		RETURN_TRUE;
	} else {
		RETURN_FALSE;
	}
}
/* }}} */

/* {{{ proto bool cast(mixed arg, string type)
   This function is basically the same as settype except that arg= NULL isn't touched */
PHP_FUNCTION(cast)
{
	zval *arg, *type;
	char *new_type;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "zz", &arg, &type) == FAILURE) {
		RETURN_FALSE;
	}
	
	/* Null values are returned as NULL */
	if (IS_NULL == Z_TYPE_P(arg)) {
		return;
	}
	
	/* This code is taken from settype() code in ext/standard/type.c */
	convert_to_string(type);
	new_type = Z_STRVAL_P(type);
	
	if (!strcasecmp(new_type, "integer")) {
		convert_to_long(arg);
	} else if (!strcasecmp(new_type, "int")) {
		convert_to_long(arg);
	} else if (!strcasecmp(new_type, "float")) {
		convert_to_double(arg);
	} else if (!strcasecmp(new_type, "double")) {
		convert_to_double(arg);
	} else if (!strcasecmp(new_type, "string")) {
		convert_to_string(arg);
	} else if (!strcasecmp(new_type, "array")) {
		convert_to_array(arg);
	} else if (!strcasecmp(new_type, "object")) {
		convert_to_object(arg);
	} else if (!strcasecmp(new_type, "bool")) {
		convert_to_boolean(arg);
	} else if (!strcasecmp(new_type, "boolean")) {
		convert_to_boolean(arg);
	} else if (!strcasecmp(new_type, "null")) {
		convert_to_null(arg);
	} else {
		zend_error(E_WARNING, "XP: Cannot convert to type '%s'", new_type);
		RETURN_FALSE;
	}
	
	*return_value= *arg;
	zval_copy_ctor(return_value);

}
/* }}} */

/* {{{ proto void try(void)
   Initialize try-block */
PHP_FUNCTION(try)
{
	TSRMLS_FETCH();
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> try\n");
	#endif	
	
	XP_CLEAR_EXCEPTION(XPG(exception));
}
/* }}} */

/* {{{ proto void catch(string name, object exception)
   Catch an exception */
PHP_FUNCTION(catch)
{
	char *name;
	int name_len;
	zval *e;
	zend_class_entry *ce= NULL;
	TSRMLS_FETCH();

	if (FAILURE == zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sz", &name, &name_len, &e)) {
		return;
	}
	
	/* Something with zval *e leaks memory here if the variable passed to catch is declared before 
	 * and I try to convert it to NULL - WHAT IS IT?! 
	 * >>> zend_execute.c(1660) :  Freeing 0x08264C64 (12 bytes)
  	 */
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> catch %s\n", XPG(exception)->object ? Z_OBJCE_P(XPG(exception)->object)->name : "n/a");
	#endif	

	if (!XPG(exception)->object) {
		RETURN_FALSE;
	}

	zend_str_tolower(name, name_len);
	for (ce= Z_OBJCE_P(XPG(exception)->object); ce != NULL; ce = ce->parent) {
		if (strncmp(name, ce->name, name_len)) continue;
		
		#ifdef XP_DEBUG
		fprintf(stderr, "xp>> caught ");
		zend_print_zval_r(XPG(exception)->object, 0);
		#endif

		object_and_properties_init(e, Z_OBJCE_P(XPG(exception)->object), Z_OBJPROP_P(XPG(exception)->object));
		zval_add_ref(&e);

		add_property_string_ex(e, "file", sizeof("file"), XPG(exception)->file ? XPG(exception)->file : "main", 1);
		add_property_long_ex(e, "line", sizeof("line"), XPG(exception)->line);
		if (XPG(exception)->class) {
			add_property_string_ex(e, "class", sizeof("class"), XPG(exception)->class, 1);
		}
		if (XPG(exception)->function) {
			add_property_string_ex(e, "function", sizeof("function"), XPG(exception)->function, 1);
		}
		
		XPG(exception)->object= NULL;
		RETURN_TRUE;
	}
	
	RETURN_FALSE; 
}
/* }}} */

/* {{{ proto bool throw(object exception)
   Throw an exception */
PHP_FUNCTION(throw)
{
	zval *object;
	zend_execute_data *ptr;
	TSRMLS_FETCH();
	
	if (FAILURE == zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &object)) {
		return;
	}
	
	/* We already have an exception */
	if (XPG(exception)->object) {
		RETURN_FALSE;
	}
	
	/* Retreive the location where the exception was thrown */
	ptr = EG(current_execute_data)->prev_execute_data;
	XP_CLEAR_EXCEPTION(XPG(exception));
	
	if (ptr) {
		if (ptr->function_state.function->common.function_name) {
			XPG(exception)->function= estrdup(ptr->function_state.function->common.function_name);
		}
		if (ptr->ce) {
			XPG(exception)->class= php_xp_get_classname(ptr->ce->name, ptr->ce->name_length);
		} else if (ptr->object.ptr) {
			XPG(exception)->class= php_xp_get_classname(ptr->object.ptr->value.obj.ce->name, ptr->object.ptr->value.obj.ce->name_length);
		}
		XPG(exception)->file = estrdup(zend_get_executed_filename(TSRMLS_C));
		XPG(exception)->line = zend_get_executed_lineno(TSRMLS_C);
	} else {
		if (zend_is_compiling(TSRMLS_C)) {
			XPG(exception)->file = estrdup(zend_get_compiled_filename(TSRMLS_C));
			XPG(exception)->line = zend_get_compiled_lineno(TSRMLS_C);
		} else if (zend_is_executing(TSRMLS_C)) {
			XPG(exception)->file = estrdup(zend_get_executed_filename(TSRMLS_C));
			XPG(exception)->line = zend_get_executed_lineno(TSRMLS_C);
		}
	}
	
	/* So, right here, is there a way to force execution towards the catch statement? */

	XPG(exception)->object = object;
	/* zval_copy_ctor(XPG(exception)->object); */
	/* zval_dtor(object); */
	zval_add_ref(&XPG(exception)->object);
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> throw %s@%s::%s (%s, line %d)\n", Z_OBJCE_P(XPG(exception)->object)->name, XPG(exception)->class, XPG(exception)->function, XPG(exception)->file, XPG(exception)->line);
	#endif
	
	RETURN_FALSE;
}
/* }}} */

/* {{{ proto void finally(void)
   Execute in case of an exception or not */
PHP_FUNCTION(finally)
{
}
/* }}} */

PHP_FUNCTION(__name)
{
	zval *arg;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &arg) == FAILURE) {
		RETURN_FALSE;
	}
	
	switch (Z_TYPE_P(arg)) {
		case IS_OBJECT: 
			RETVAL_STRING(php_xp_get_classname(Z_OBJCE_P(arg)->name, Z_OBJCE_P(arg)->name_length), 0);
			break;
			
		case IS_STRING:
			RETVAL_STRING(php_xp_get_classname(Z_STRVAL_P(arg), Z_STRLEN_P(arg)), 0);
			break;
		
		default:
			RETURN_FALSE;
	}
}

/* {{{ proto void __construct([array])
   Constructor */
PHP_FUNCTION(__construct)
{
	zval *array;
	
	XP_NOT_STATIC();
	
	#ifdef XP_DEBUG
	fprintf(stderr, "xp>> %s::__construct(%d)\n", Z_OBJCE_P(this_ptr)->name, ZEND_NUM_ARGS());
	#endif
	
	/* If the first parameter is an array, merge it with the object's properties */
	if (1 != ZEND_NUM_ARGS()) return;
	if ((SUCCESS == zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &array)) && (IS_ARRAY == Z_TYPE_P(array))) {
		zval *tmp;
		
		zend_hash_merge(Z_OBJPROP_P(this_ptr), Z_ARRVAL_P(array), (copy_ctor_func_t) zval_add_ref, (void *) &tmp, sizeof(zval *), 1);
	}
}
/* }}} */

/* {{{ proto string getclass(void)
   Get class object */
PHP_FUNCTION(getclass)
{
	zend_class_entry *ce = NULL;
	zval *retval = NULL;
	zval *function;
	zval **args[1]= { &this_ptr };
	XP_NOT_STATIC();
	
	/* Initialize a new XPClass object */
	if (FAILURE == zend_hash_find(EG(class_table), "xpclass", sizeof("xpclass"), (void **)&ce)) {
		zend_error(E_WARNING, "XP: XPClass class not registered");
		RETURN_FALSE;
	}
	
	object_init_ex(return_value, ce);
	
	/* Call its constructor */
	ALLOC_ZVAL(function);
	INIT_PZVAL(function);
	ZVAL_STRING(function, XP_CTOR, 1);
	call_user_function_ex(
		EG(function_table),
		&return_value, 
		function,
		&retval, 
		1,
		args, 
		0, 
		NULL TSRMLS_CC
	);
	
	/* Free up zvals */
	zval_ptr_dtor(&function);
	if (retval) {
		zval_ptr_dtor(&retval);
	}
}
/* }}} */

/* {{{ proto string tostring(void)
   Create string representation of an object */
PHP_FUNCTION(tostring)
{
	php_serialize_data_t var_hash;
	smart_str buf = {0};
	
	XP_NOT_STATIC();
	
	PHP_VAR_SERIALIZE_INIT(var_hash);
	php_var_serialize(&buf, &this_ptr, &var_hash TSRMLS_CC);
	PHP_VAR_SERIALIZE_DESTROY(var_hash);
	
	if (buf.c) {
		char *c, *str;
		
		c= php_xp_get_classname(Z_OBJCE_P(this_ptr)->name, Z_OBJCE_P(this_ptr)->name_length);
		str= (char *)erealloc(c, strlen(c) + buf.len + 2);
		strcat(str, "@");
		strcat(str, buf.c);
		ZVAL_STRING(return_value, str, 1);
		efree(buf.c);
		efree(str);
	} else {
		RETURN_NULL();
	}
}
/* }}} */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
