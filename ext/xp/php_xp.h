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
  | Author:                                                              |
  +----------------------------------------------------------------------+

  $Id$ 
*/

#ifndef PHP_XP_H
#define PHP_XP_H

extern zend_module_entry xp_module_entry;
#define phpext_xp_ptr &xp_module_entry

#ifdef PHP_WIN32
#define PHP_XP_API __declspec(dllexport)
#else
#define PHP_XP_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(xp);
PHP_MSHUTDOWN_FUNCTION(xp);
PHP_RINIT_FUNCTION(xp);
PHP_RSHUTDOWN_FUNCTION(xp);
PHP_MINFO_FUNCTION(xp);

/* Global functions */
PHP_FUNCTION(uses);
PHP_FUNCTION(cast);
PHP_FUNCTION(try);
PHP_FUNCTION(catch);
PHP_FUNCTION(throw);
PHP_FUNCTION(finally);
PHP_FUNCTION(__name);

/* Class methods */
PHP_FUNCTION(__construct);
PHP_FUNCTION(getclass);
PHP_FUNCTION(tostring);

typedef struct {
	char *name;
	int name_len;
} php_xp_classlist;

typedef struct {
	zval *object;
	char *file;
	char *function;
	char *class;
	int line;
} php_xp_exception;

ZEND_BEGIN_MODULE_GLOBALS(xp)
	char *class_path;
	HashTable names;
	php_xp_exception *exception;
ZEND_END_MODULE_GLOBALS(xp)

/* In every utility function you add that needs to use variables 
   in php_xp_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as XPG(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define XPG(v) TSRMG(xp_globals_id, zend_xp_globals *, v)
#else
#define XPG(v) (xp_globals.v)
#endif

#define XP_NOT_STATIC() \
        if (!this_ptr) { \
                zend_error(E_WARNING, "XP: %s() cannot be called statically", get_active_function_name(TSRMLS_C)); \
                return; \
        }
		
#define XP_CLEAR_EXCEPTION(e) \
		if (e->file) efree(e->file); \
		e->file = NULL; \
		e->line = 0; \
		if (e->function) efree(e->function); \
		e->function = NULL; \
		if (e->class) efree(e->class); \
		e->class= NULL; \
		if (e->object) zval_ptr_dtor(&e->object); \
		e->object= NULL;

#endif	/* PHP_XP_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
