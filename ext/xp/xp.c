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
#include "zend.h"
#include "zend_compile.h"
#include "zend_API.h"

#define XP_DEBUG            (1<<0L)
#define XP_LOG              (1<<1L)
#define XP_INFO             (1<<2L)
#define XP_WARNING          (1<<3L)
#define XP_ERROR            (1<<4L)
#define XP_FATAL            (1<<5L)

#define XP_ERROR_FMT_STR    "[xp $Revision$] %-11s"
#define XP_ERROR_FMT_LEN    32

ZEND_DECLARE_MODULE_GLOBALS(xp)

/* {{{ xp_functions[]
 *     Global functions
 */
static function_entry xp_functions[] = {
	PHP_FE(uses,	NULL)
#ifdef XP_DEBUGGING
    PHP_FE(testnames, NULL)
#endif
	{ NULL, NULL, NULL }
};
/* }}} */

/* {{{ xp_object_functions[]
 *     Class methods for class lang::Object
 */
static function_entry xp_object_functions[] = {
	PHP_FE(equals, NULL)
	PHP_FE(tostring, NULL)
	PHP_FE(getclassname, NULL)
	{NULL, NULL, NULL}
};
/* }}} */

/* {{{ xp_throwable_functions[]
 *     Class methods for class lang::Throwable
 */
static function_entry xp_throwable_functions[] = {
	PHP_NAMED_FE(__construct,   PHP_FN(throwable___construct),      NULL)
	PHP_NAMED_FE(tostring,      PHP_FN(throwable_tostring),         NULL)
	PHP_NAMED_FE(getmessage,    PHP_FN(throwable_getmessage),       NULL)
	PHP_NAMED_FE(getcode,       PHP_FN(throwable_getcode),          NULL)
	PHP_NAMED_FE(getfile,       PHP_FN(throwable_getfile),          NULL)
	PHP_NAMED_FE(getline,       PHP_FN(throwable_getline),          NULL)
	{NULL, NULL, NULL}
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

#ifdef COMPILE_DL_XP
ZEND_GET_MODULE(xp)
#endif

/* {{{ PHP_INI
 */
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("xp.class_path", "/usr/home/thekid/devel/xp/skeleton", PHP_INI_ALL, OnUpdateString, class_path, zend_xp_globals, xp_globals)
PHP_INI_END()
/* }}} */

/* {{{ panter_error - error handler */
static void xp_error(int type, char* msg, ...)
{	
    va_list ap;
    char *txt;
    int msg_len= strlen(msg);
    
    txt= (char*)malloc(XP_ERROR_FMT_LEN + msg_len+ 1);
    switch (type) {
        case XP_DEBUG:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Debug");
            break;
            
        case XP_LOG:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Log");
            break;

        case XP_INFO:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Information");
            break;
            
        case XP_WARNING:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Warning");
            break;
            
        case XP_ERROR:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Error");
            break;
            
        case XP_FATAL:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "Fatal");
            break;
            
        default:
            snprintf(txt, XP_ERROR_FMT_LEN, XP_ERROR_FMT_STR, "(Unknown)");
            break;
    }
    strncat(txt, msg, msg_len);
    strncat(txt, "\n", 1);
	va_start(ap, msg);
	vfprintf(stderr, txt, ap);
	va_end(ap);
    
    free(txt);
    
    if (type == XP_FATAL) zend_bailout();
}
/* }}} */

/* {{{ php_xp_init_globals
 */
static void php_xp_init_globals(zend_xp_globals *xp_globals TSRMLS_DC)
{
    xp_error(XP_DEBUG, "php_xp_init_globals");

	xp_globals->class_path = NULL;
    zend_hash_init(&xp_globals->names, 10, NULL, ZVAL_PTR_DTOR, 1);
}
/* }}} */

/* {{{ php_xp_destroy_globals
 */
static void php_xp_destroy_globals(zend_xp_globals *xp_globals TSRMLS_DC)
{
	#ifdef XP_DEBUGGING
	fprintf(stderr, "xp>> php_xp_destroy_globals\n");
	#endif

    zend_hash_destroy(&xp_globals->names);
}
/* }}} */

static zend_class_entry* xp_register_class(char* name, int name_len, char* prettyname, int prettyname_len, zend_function_entry* functions, zend_namespace* namespace, zend_class_entry* parent)
{
    zend_class_entry ce;
    zend_class_entry* ptr;
    char* fullname;
    zval* xpname;
    
	INIT_CLASS_ENTRY(ce, name, functions);
	ptr = zend_register_internal_ns_class(&ce, parent, namespace, namespace->name TSRMLS_CC);
    
    fullname= (char*) emalloc(name_len+ 2+ namespace->name_length+ 1);
    strncpy(fullname, namespace->name, namespace->name_length+ 1);
    strncat(fullname, "::", 2);
    strncat(fullname, name, name_len);

    MAKE_STD_ZVAL(xpname);
    ZVAL_STRINGL(xpname, prettyname, prettyname_len, 1);
    
    zend_hash_update(&XPG(names), fullname, name_len+ 2+ namespace->name_length, &xpname, sizeof(zval *), NULL);
    
    efree(fullname);
    
    return ptr;
}

#define XN(name, fullname) name, sizeof(name), fullname, sizeof(fullname)

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(xp)
{
	zend_namespace xp_lang_namespace;
    zend_namespace* np;
    zend_class_entry* xp_object_ptr;
    zend_class_entry* xp_throwable_ptr;

	ZEND_INIT_MODULE_GLOBALS(xp, php_xp_init_globals, php_xp_destroy_globals);
	REGISTER_INI_ENTRIES();

	#ifdef XP_DEBUGGING
	fprintf(stderr, "xp>> PHP_MINIT_FUNCTION\n");
	#endif
	
	/* Register "lang" namespace */
	INIT_NAMESPACE(xp_lang_namespace, "lang");
	np= zend_register_internal_namespace(&xp_lang_namespace TSRMLS_C);

    /* Register basic classes */
    xp_object_ptr = xp_register_class(XN("object", "lang.Object"), xp_object_functions, np, NULL);
    xp_throwable_ptr = xp_register_class(XN("throwable", "lang.Throwable"), xp_throwable_functions, np, xp_object_ptr);
	xp_register_class(XN("error", "lang.Error"), NULL, np, xp_throwable_ptr);
    xp_register_class(XN("exception", "lang.Exception"), NULL, np, xp_throwable_ptr);
	
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(xp)
{
	#ifdef XP_DEBUGGING
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
	#ifdef XP_DEBUGGING
	fprintf(stderr, "xp>> PHP_RINIT_FUNCTION\n");
	#endif

	/* Set error reporting to E_ALL */
	EG(error_reporting) = E_ALL;
	
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(xp)
{
	#ifdef XP_DEBUGGING
	fprintf(stderr, "xp>> PHP_RSHUTDOWN_FUNCTION\n");
	#endif
    zend_hash_clean(&XPG(names));
    
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(xp)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "XP Framework", "enabled");
	php_info_print_table_row(2, "Version", "$Id$");
	php_info_print_table_end();

	DISPLAY_INI_ENTRIES();
}
/* }}} */

static void _get_fully_qualified_class_name(zend_class_entry* ce, char** str, int* str_len, int reserve)
{
	int ns_len = 0;

	*str_len= ce->name_length + reserve + 1;
	
	if (ce->ns 
		&& ce->ns != &CG(global_namespace) 
		&& ce->ns->name) {
		ns_len = ce->ns->name_length + 2;
		*str_len += ns_len;
	}

	*str= (char*) emalloc(*str_len);
	if (ns_len > 0) {
		strncpy(*str, ce->ns->name, ns_len);
		strncat(*str, "::", 2);
	} else {
		*str[0]= '\0';
	}
	strncat(*str, ce->name, ce->name_length);	
}

static void smart_str_appendc_n(smart_str* buf, const char c, int num) 
{
	int i;

	for (i = 0; i < num; i++) {
		smart_str_appendc(buf, c);
	}
}

/* _var_src */
static void _var_src(smart_str *buf, zval **struc, int level TSRMLS_DC);

static int _array_element_export(zval **zv, int num_args, va_list args, zend_hash_key *hash_key)
{
	int level;
	smart_str* buf;
	TSRMLS_FETCH();

	level = va_arg(args, int);
	buf= va_arg(args, smart_str*);
	
	smart_str_appendc_n(buf, ' ', level);
	if (hash_key->nKeyLength == 0) {
		smart_str_append_long(buf, hash_key->h);
	} else {
		smart_str_appendc(buf, '\'');
		smart_str_appendl(buf, hash_key->arKey, hash_key->nKeyLength);
		smart_str_appendc(buf, '\'');
	}
	smart_str_appendl(buf, " => ", 4);
	_var_src(buf, zv, level + 2 TSRMLS_CC);
	smart_str_appendl(buf, ",\n", 2);
	return 0;
}

static int _object_property_export(zend_property_info *prop_info, int num_args, va_list args, zend_hash_key *hash_key)
{
	int level;
	smart_str* buf;
	zval* object;
	zval** member = NULL;
	int found;
	TSRMLS_FETCH();

	if (hash_key->nKeyLength == 0) return 0;	/* Ignore numeric */
	
	level = va_arg(args, int);
	buf = va_arg(args, smart_str*);
	object = va_arg(args, zval*);

	smart_str_appendc_n(buf, ' ', level);
	switch (prop_info->flags & ZEND_ACC_PPP_MASK) {
		case ZEND_ACC_PUBLIC:
			smart_str_appendl(buf, "public", 6);
			break;
		case ZEND_ACC_PRIVATE:
			smart_str_appendl(buf, "private", 7);
			break;
		case ZEND_ACC_PROTECTED:
			smart_str_appendl(buf, "protected", 9);
			break;
		default:
			smart_str_appendl(buf, "var", 3);   /* This should not occur */
			break;
	}
	if (prop_info->flags & ZEND_ACC_STATIC) {
		smart_str_appendl(buf, " static", 7);
		found= zend_hash_find(Z_OBJCE_P(object)->static_members, prop_info->name, prop_info->name_length + 1, (void**) &member);
	} else {
		found= zend_hash_find(Z_OBJPROP_P(object), prop_info->name, prop_info->name_length + 1, (void**) &member);
	}
	smart_str_appendl(buf, " $", 2);
	smart_str_appendl(buf, hash_key->arKey, hash_key->nKeyLength - 1);

	/* Find corresponding zval and get its string representation */
	if (found == SUCCESS) {
		smart_str_appendl(buf, "= ", 2);
		_var_src(buf, member, level + 2 TSRMLS_CC);
	}
	smart_str_appendl(buf, ";\n", 2);
	return 0;
}

static void _var_src(smart_str *buf, zval **struc, int level TSRMLS_DC)
{
	HashTable *myht;

	switch (Z_TYPE_PP(struc)) {
		case IS_BOOL:
			smart_str_appendl(buf, Z_LVAL_PP(struc) ? "true" : "false", Z_LVAL_PP(struc) ? 4 : 5);
			break;

		case IS_NULL:
			smart_str_appendl(buf, "NULL", 4);
			break;

		case IS_LONG:
			smart_str_append_long(buf, Z_LVAL_PP(struc));
			break;

		case IS_DOUBLE: {
			char s[256];
			ulong slen;

			slen = sprintf(s, "%.*G", (int) EG(precision), Z_DVAL_PP(struc));
			smart_str_appendl(buf, s, slen);
			break;
			}

		case IS_STRING:
			smart_str_appendc(buf, '\'');
			smart_str_appendl(buf, Z_STRVAL_PP(struc),  Z_STRLEN_PP(struc));
			smart_str_appendc(buf, '\'');
			break;

		case IS_ARRAY:
			myht = Z_ARRVAL_PP(struc);
			if (myht->nApplyCount > 1) {
				smart_str_appendl(buf, "*RECURSION*\n", 12);
				return;
			}
			smart_str_appendl(buf, "array(\n", 7);
			zend_hash_apply_with_arguments(myht, (apply_func_args_t) _array_element_export, 2, level, buf);
			smart_str_appendc_n(buf, ' ', level - 2);
			smart_str_appendc(buf, ')');
			break;

		case IS_OBJECT: {
			char *class_name;
			int class_name_len;

			myht = &Z_OBJCE_PP(struc)->properties_info;
			if (myht->nApplyCount > 1) {
				smart_str_appendl(buf, "*RECURSION*\n", 12);
				return;
			}
			_get_fully_qualified_class_name(Z_OBJCE_PP(struc), &class_name, &class_name_len, 0);
			smart_str_appendl(buf, class_name, class_name_len - 1);
			smart_str_appendl(buf, " {\n", 3);		
			efree(class_name);
			if (myht) {
				zend_hash_apply_with_arguments(myht, (apply_func_args_t) _object_property_export, 3, level, buf, *struc);
			}
			smart_str_appendc_n(buf, ' ', level - 2);
			smart_str_appendc(buf, '}');
			break;
			}

		default:
			smart_str_appendl(buf, "NULL", 4);
			break;
	}
}

/* {{{ proto bool Object::equals(Object o)
       Indicates whether some other object is "equal to" this one. */
PHP_FUNCTION(equals)
{
	zval *object;
	METHOD_NOT_STATIC;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &object) == FAILURE) {
		RETURN_FALSE;
	}

	RETVAL_BOOL(
		Z_OBJ_HT_P(getThis()) == Z_OBJ_HT_P(object) && 
	 	Z_OBJ_HANDLE_P(getThis()) == Z_OBJ_HANDLE_P(object)
	);
}
/* }}} */

/* {{{ proto string Object::toString(void)
       Creates a string representation of this object */
PHP_FUNCTION(tostring)
{
	smart_str buf = {0};
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;
	
	_var_src(&buf, &getThis(), 2 TSRMLS_CC);
	smart_str_0(&buf);
	RETVAL_STRINGL(buf.c, buf.len, 0);
}
/* }}} */

/* {{{ proto string Object::getClassName(void)
       Returns fully qualified class name */
PHP_FUNCTION(getclassname)
{   
	char *name;
	int name_len;
    zval **xpname= NULL;
    
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;

    _get_fully_qualified_class_name(Z_OBJCE_P(getThis()), &name, &name_len, 0);	
    if (FAILURE == zend_hash_find(&XPG(names), name, name_len, (void**)&xpname)) {
        zend_error(E_WARNING, "Could not find class name for %s\n", name);
        RETVAL_FALSE;
    } else {
        RETVAL_STRINGL(Z_STRVAL_PP(xpname), Z_STRLEN_PP(xpname), 1);
    }
    efree(name);
}
/* }}} */

/* {{{ proto Throwable Throwable::__construct([string message [, int code]])
       Constructor */
PHP_FUNCTION(throwable___construct)
{
	zval **message;
	zval **code;
	zval  *tmp;
	zval  *object;
	int	argc = ZEND_NUM_ARGS();

	if (zend_get_parameters_ex(argc, &message, &code) == FAILURE) {
		ZEND_WRONG_PARAM_COUNT();
	}

	object = getThis();

	if (argc > 0) {
		convert_to_string_ex(message);
		zval_add_ref(message);
		zend_hash_update(Z_OBJPROP_P(object), "message", sizeof("message"), (void **) message, sizeof(zval *), NULL);
	}

	if (argc > 1) {
		convert_to_long_ex(code);
		zval_add_ref(code);
		zend_hash_update(Z_OBJPROP_P(object), "code", sizeof("code"), (void **) code, sizeof(zval *), NULL);
	}

	MAKE_STD_ZVAL(tmp);
	ZVAL_STRING(tmp, zend_get_executed_filename(TSRMLS_C), 1);
	zend_hash_update(Z_OBJPROP_P(object), "file", sizeof("file"), (void **) &tmp, sizeof(zval *), NULL);
	tmp = NULL;

	MAKE_STD_ZVAL(tmp);
	ZVAL_LONG(tmp, zend_get_executed_lineno(TSRMLS_C));
	zend_hash_update(Z_OBJPROP_P(object), "line", sizeof("line"), (void **) &tmp, sizeof(zval *), NULL);
}
/* }}} */

static void _default_exception_get_entry(zval *object, char *name, int name_len, zval *return_value TSRMLS_DC)
{
	zval **value;

	if (zend_hash_find(Z_OBJPROP_P(object), name, name_len, (void **) &value) == FAILURE) {
		RETURN_FALSE;
	}

	*return_value = **value;
	zval_copy_ctor(return_value);
}

/* {{{ proto string Throwable::getFile(void)
       Retreives file in which this exception was thrown */
PHP_FUNCTION(throwable_getfile)
{
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;

	_default_exception_get_entry(getThis(), "file", sizeof("file"), return_value TSRMLS_CC);
}
/* }}} */

/* {{{ proto int Throwable::getLine(void)
       Retreives line on which this exception was thrown */
PHP_FUNCTION(throwable_getline)
{
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;

	_default_exception_get_entry(getThis(), "line", sizeof("line"), return_value TSRMLS_CC);
}
/* }}} */

/* {{{ proto string Throwable::getMessage(void)
       Retreives exception message */
PHP_FUNCTION(throwable_getmessage)
{
	METHOD_NOT_STATIC
	DEFAULT_0_PARAMS;

	_default_exception_get_entry(getThis(), "message", sizeof("message"), return_value TSRMLS_CC);
}
/* }}} */

/* {{{ proto int Throwable::getCode(void)
       Retreives exception code */
PHP_FUNCTION(throwable_getcode)
{
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;

	_default_exception_get_entry(getThis(), "code", sizeof("code"), return_value TSRMLS_CC);
}
/* }}} */

/* {{{ proto string Throwable::toString(void)
       Creates string representation */
PHP_FUNCTION(throwable_tostring)
{
	char *name;
	int name_len;
	zval **message, **file, **line, **code;
	smart_str buf = {0};
	METHOD_NOT_STATIC;
	DEFAULT_0_PARAMS;

	if ((zend_hash_find(Z_OBJPROP_P(getThis()), "file", sizeof("file"), (void **) &file) == FAILURE) 
		|| (zend_hash_find(Z_OBJPROP_P(getThis()), "line", sizeof("line"), (void **) &line) == FAILURE)) {
		RETURN_FALSE;
	}
	
	_get_fully_qualified_class_name(Z_OBJCE_P(getThis()), &name, &name_len, 0);
	smart_str_appendl(&buf, name, name_len - 1);
	efree(name);
	smart_str_appendl(&buf, " (", 2);
	if (zend_hash_find(Z_OBJPROP_P(getThis()), "message", sizeof("message"), (void **) &message) == SUCCESS) {
		smart_str_appendl(&buf, Z_STRVAL_PP(message), Z_STRLEN_PP(message));
	}
	if (zend_hash_find(Z_OBJPROP_P(getThis()), "code", sizeof("code"), (void **) &code) == SUCCESS) {
		if (message) smart_str_appendc(&buf, ' ');
        smart_str_appendc(&buf, '[');
		smart_str_appendl(&buf, "code ", 6);
		smart_str_append_long(&buf, Z_LVAL_PP(code));
        smart_str_appendc(&buf, ']');
	}
	smart_str_appendl(&buf, ") {\n", 4);
	zend_hash_apply_with_arguments(&Z_OBJCE_P(getThis())->properties_info, (apply_func_args_t) _object_property_export, 3, 4, &buf, getThis());
	smart_str_appendl(&buf, "  }\n  at ", 9);
	smart_str_appendl(&buf, Z_STRVAL_PP(file), Z_STRLEN_PP(file));
	smart_str_appendc(&buf, ':');
	smart_str_append_long(&buf, Z_LVAL_PP(line));
	smart_str_0(&buf);

	RETVAL_STRINGL(buf.c, buf.len, 0);
}
/* }}} */

/* {{{ php_xp_uses
 */
static int php_xp_uses(char *arg, int arg_len TSRMLS_DC)
{
	char *eval = (char*)emalloc(arg_len+ sizeof("include_once('.class.php');"));
	char *desc;
	int i, return_value;
	zval retval;
	
	/* Build complete path to file */
    strncpy(eval, "include_once('", 14);
	for (i = 0; i < arg_len; i++) {
        eval[i+ 14]= ('.' == arg[i]) ? DEFAULT_SLASH : arg[i];
	}
	eval[i+ 14]= '\0';
	strncat(eval, ".class.php');", 13);

	/* Evaluate string */
	desc= zend_make_compiled_string_description("uses" TSRMLS_CC);	
	if (FAILURE == zend_eval_string(eval, &retval, desc TSRMLS_CC)) {
		efree(desc);
		zend_error(E_CORE_ERROR, "XP: Core error during execution of '%s'", eval);
		
		/* Bails out */
	}
	efree(desc);
	efree(eval);
	convert_to_boolean(&retval);
    return_value = Z_BVAL(retval);
    zval_dtor(&retval);

	#ifdef XP_DEBUGGING
	fprintf(stderr, "xp>> uses(string(%d)'%s') = %d\n", arg_len, arg, return_value);
	#endif
    
    if (return_value) {
        char* fullname= estrndup(arg, arg_len+ 1);
        char* name= estrdup(strrchr(fullname, '.'));
        int name_len= strlen(name);
        zval* xpname;
        
	    for (i = 0; i < arg_len - name_len; i++) {
            switch (fullname[i]) {
                case '.' : fullname[i]= ':'; break;
                case '-' : fullname[i]= '_'; break;
            }
	    }
        fullname[i]= '\0';
        strncat(fullname, "::", 2);
        zend_str_tolower(name++, name_len);
        strncat(fullname, name, name_len);
        name--;

        MAKE_STD_ZVAL(xpname);
        ZVAL_STRINGL(xpname, arg, arg_len, 1);
        zend_hash_update(&XPG(names), fullname, arg_len+ 2, &xpname, sizeof(zval *), NULL);
        
        efree(name);
        efree(fullname);
    }

	return return_value;
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

#ifdef XP_DEBUGGING
static int _print_values(zval **zv, int num_args, va_list args, zend_hash_key *hash_key)
{
    if (hash_key->nKeyLength == 0) {
        fprintf(stderr, "--- int(%ld) => ", hash_key->h);
    } else {
	    fprintf(stderr, "--- string(%d)'%s' => ", hash_key->nKeyLength, hash_key->arKey);	    
    }    
	zend_print_zval_r(*zv, 0);
	fprintf(stderr, "\n");
	return 0;
}

PHP_FUNCTION(testnames)
{
    zend_hash_apply_with_arguments(&XPG(names), (apply_func_args_t) _print_values, 0);

    RETURN_TRUE;
}
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
