/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2006 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id: header,v 1.16.2.1 2006/01/01 12:50:00 sniper Exp $ */

#ifndef PHP_THREADS_H
#define PHP_THREADS_H

extern zend_module_entry threads_module_entry;
#define phpext_threads_ptr &threads_module_entry

#ifdef PHP_WIN32
#define PHP_THREADS_API __declspec(dllexport)
#else
#define PHP_THREADS_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(threads);
PHP_MSHUTDOWN_FUNCTION(threads);
PHP_RINIT_FUNCTION(threads);
PHP_RSHUTDOWN_FUNCTION(threads);
PHP_MINFO_FUNCTION(threads);

PHP_FUNCTION(thread_new);
PHP_FUNCTION(thread_start);
PHP_FUNCTION(thread_join);

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     

ZEND_BEGIN_MODULE_GLOBALS(threads)
	long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(threads)
*/

#include <pthread.h>

typedef struct _php_thread_event {
	pthread_mutex_t* mutex;
	pthread_cond_t* cond;
} php_thread_event;

typedef struct {
	char* name;
    int name_len;
	pthread_t* thread;
    void* context;
    zend_class_entry* scope;
    zval* object;
    zend_function* callable;
    zval ***arguments;
    int argument_count;
} php_thread_ptr;


/* In every utility function you add that needs to use variables 
   in php_threads_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as THREADS_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define THREADS_G(v) TSRMG(threads_globals_id, zend_threads_globals *, v)
#else
#define THREADS_G(v) (threads_globals.v)
#endif

#endif	/* PHP_THREADS_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
