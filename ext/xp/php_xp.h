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

#ifndef PHP_XP_H
#define PHP_XP_H

#define XP_DEBUG 1

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
#ifdef XP_DEBUG
PHP_FUNCTION(testnames);
#endif

/* Object methods */
PHP_FUNCTION(equals);
PHP_FUNCTION(tostring);
PHP_FUNCTION(getclassname);

/* Throwable methods */
PHP_FUNCTION(throwable___construct);
PHP_FUNCTION(throwable_tostring);
PHP_FUNCTION(throwable_getmessage);
PHP_FUNCTION(throwable_getcode);
PHP_FUNCTION(throwable_getfile);
PHP_FUNCTION(throwable_getline);

ZEND_BEGIN_MODULE_GLOBALS(xp)
	char *class_path;
	HashTable names;
ZEND_END_MODULE_GLOBALS(xp)

#ifdef ZTS
#define XPG(v) TSRMG(xp_globals_id, zend_xp_globals *, v)
#else
#define XPG(v) (xp_globals.v)
#endif

#define DEFAULT_0_PARAMS \
	if (ZEND_NUM_ARGS() > 0) { \
		ZEND_WRONG_PARAM_COUNT(); \
	}

#define METHOD_NOT_STATIC \
        if (!this_ptr) { \
                zend_error(E_ERROR, "XP: %s() cannot be called statically", get_active_function_name(TSRMLS_C)); \
                return; \
        }
		
#endif	/* PHP_XP_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
