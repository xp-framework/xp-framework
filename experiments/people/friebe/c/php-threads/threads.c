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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_threads.h"
#include "SAPI.h"

/* If you declare any globals in php_threads.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(threads)
*/

/* True global resources - no need for thread safety here */
static int le_threads;

/* {{{ threads_functions[]
 *
 * Every user visible function must have an entry in threads_functions[].
 */
zend_function_entry threads_functions[] = {
	PHP_FE(thread_new, NULL)
	PHP_FE(thread_start, NULL)
	PHP_FE(thread_join, NULL)
	{NULL, NULL, NULL}	/* Must be the last line in threads_functions[] */
};
/* }}} */

/* {{{ threads_module_entry
 */
zend_module_entry threads_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"threads",
	threads_functions,
	PHP_MINIT(threads),
	PHP_MSHUTDOWN(threads),
	PHP_RINIT(threads),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(threads),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(threads),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_THREADS
ZEND_GET_MODULE(threads)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("threads.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_threads_globals, threads_globals)
    STD_PHP_INI_ENTRY("threads.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_threads_globals, threads_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_threads_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_threads_init_globals(zend_threads_globals *threads_globals)
{
	threads_globals->global_value = 0;
	threads_globals->global_string = NULL;
}
*/
/* }}} */

static php_thread_event* create_event(void)
{
	php_thread_event* e= (php_thread_event*) malloc(sizeof(php_thread_event));
	e->mutex = (pthread_mutex_t *) malloc(sizeof(pthread_mutex_t));
	e->cond = (pthread_cond_t *) malloc(sizeof(pthread_cond_t));
	pthread_mutex_init(e->mutex, NULL);
	pthread_cond_init(e->cond, NULL);
	
	return e;
}

static void release_event(php_thread_event* e)
{
	pthread_cond_destroy(e->cond);
	pthread_mutex_destroy(e->mutex);
	free(e->cond);
	free(e->mutex); 
	free(e);   
}

static int wait_event(php_thread_event* e)
{
	int r;
	
	pthread_mutex_lock(e->mutex);
	r= pthread_cond_wait(e->cond, e->mutex);
	pthread_mutex_unlock(e->mutex);
	return r;
}

static int set_event(php_thread_event* e)
{
	int r;
	
	pthread_mutex_lock(e->mutex);
	r= pthread_cond_broadcast(e->cond);
	pthread_mutex_unlock(e->mutex);
	return r;
}

static void _free_thread(zend_rsrc_list_entry *rsrc TSRMLS_DC)
{
    php_thread_ptr* ptr= (php_thread_ptr*) rsrc->ptr;
    if (ptr->thread) {
        efree(ptr->thread);	
    }
	if (ptr->context) {
        tsrm_set_interpreter_context(NULL);
        tsrm_free_interpreter_context(ptr->context);
    }
    if (ptr->name) {
        efree(ptr->name);
    }
}

static void* php_threads_run(void *arg)
{
    zval *retval= NULL;
    php_thread_ptr* ptr= (php_thread_ptr*)arg;    
    void *prior_context= tsrm_set_interpreter_context(ptr->context); 
    TSRMLS_FETCH();

    /* Startup engine */
	SG(headers_sent)= 1;
	SG(request_info).no_headers= 1;
	SG(options)= SAPI_OPTION_NO_CHDIR;
	php_request_startup(TSRMLS_C);
	PG(during_request_startup)= 0;

    zend_first_try {
		EG(return_value_ptr_ptr)= &retval;
		EG(active_op_array)= (zend_op_array *) ptr->callable;
        EG(scope)= ptr->scope;
        EG(This)= ptr->object;
		ALLOC_HASHTABLE(EG(active_symbol_table));
		zend_hash_init(EG(active_symbol_table), 0, NULL, ZVAL_PTR_DTOR, 0);
        
        /* Arguments */
        if (ptr->arguments) {
            int i= 0;
            zval* param;
            
            for (i= 0; i < ptr->argument_count; i++) {
			    ALLOC_ZVAL(param);
			    *param = **(ptr->arguments[i]);
			    INIT_PZVAL(param);
                
                zend_ptr_stack_push(&EG(argument_stack), param);
            }
            
            zend_ptr_stack_2_push(&EG(argument_stack), (void *) (long) ptr->argument_count, NULL);
        }
        
		zend_execute(EG(active_op_array) TSRMLS_CC);
        
        EG(active_op_array)= NULL;
	} zend_catch {
	    fprintf(stderr, "***BAILED!");
	} zend_end_try();

    /* Shutdown engine */
	php_request_shutdown(TSRMLS_C);
    
    tsrm_set_interpreter_context(prior_context); 
    pthread_exit(retval);
}

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(threads)
{
	le_threads = zend_register_list_destructors_ex(_free_thread, NULL, "Thread", module_number);
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(threads)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(threads)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(threads)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(threads)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "threads support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */


/* {{{ proto resource thread_new(string name)
   Creates a new thread */
PHP_FUNCTION(thread_new)
{
    php_thread_ptr* ptr= NULL;
	char *name_str;
	int name_len;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &name_str, &name_len) == FAILURE) {
		return;
	}
    
	ptr = (php_thread_ptr *) emalloc(sizeof(php_thread_ptr));
    ptr->name= estrndup(name_str, name_len);
    ptr->name_len= name_len;
    ptr->context= NULL;
    ptr->thread= (pthread_t*) emalloc(sizeof(pthread_t));
    
    ZEND_REGISTER_RESOURCE(return_value, ptr, le_threads);
}
/* }}} */

/* {{{ proto bool thread_start(resource thread, callable function)
   Runs the specified thread */
PHP_FUNCTION(thread_start)
{
	zval **resource;
	zval *arguments= NULL;
    zval *callable;
    php_thread_ptr* ptr= NULL;
    int r;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "zz|a", resource, &callable, &arguments) == FAILURE) {
		return;
	}

	ZEND_FETCH_RESOURCE(ptr, php_thread_ptr *, resource, -1, "Thread", le_threads);

    /* Lookup callback function */
    switch (Z_TYPE_P(callable)) {
        case IS_STRING: {
	        if (zend_hash_find(EG(function_table), Z_STRVAL_P(callable), Z_STRLEN_P(callable) + 1, (void**) &ptr->callable) == FAILURE) {
		        zend_error(E_WARNING, "Given argument %s is not callable", Z_STRVAL_P(callable));
		        RETURN_FALSE;
	        }
            ptr->scope= NULL;
            ptr->object= NULL;
            break;
        }

        case IS_ARRAY: {
			zval **method;
			zval **obj;

			if (zend_hash_num_elements(Z_ARRVAL_P(callable)) == 2 &&
			    zend_hash_index_find(Z_ARRVAL_P(callable), 0, (void **) &obj) == SUCCESS &&
			    zend_hash_index_find(Z_ARRVAL_P(callable), 1, (void **) &method) == SUCCESS
            ) {
                if (Z_TYPE_PP(obj) == IS_OBJECT) {      /* array($object, $method) */
	                ptr->scope= Z_OBJCE_PP(obj);
                    
                    /* XXX FIXME XXX Needs to be a proxy or something (read/write/call handlers overridden?) */
                    ptr->object= *obj;
                    ptr->object->refcount++;
                } else {                                /* array($classname, $method) */
                    zend_class_entry **pce= NULL;
                    char* lcname;
                    
                    convert_to_string_ex(obj);
					lcname= zend_str_tolower_dup(Z_STRVAL_PP(obj), Z_STRLEN_PP(obj));
					if (Z_STRLEN_PP(obj) == sizeof("self") - 1 && memcmp(lcname, "self", sizeof("self")) == 0) {
						ptr->scope= EG(active_op_array)->scope;
					} else if (Z_STRLEN_PP(obj) == sizeof("this") - 1 && memcmp(lcname, "this", sizeof("this")) == 0 && EG(This)) {
						ptr->scope= Z_OBJCE_P(EG(This));
					} else if (Z_STRLEN_PP(obj) == sizeof("parent") - 1 && memcmp(lcname, "parent", sizeof("parent")) == 0 && EG(active_op_array)->scope) {
						ptr->scope= EG(active_op_array)->scope->parent;
					} else if (zend_lookup_class(Z_STRVAL_PP(obj), Z_STRLEN_PP(obj), &pce TSRMLS_CC) == SUCCESS) {
						ptr->scope= *pce;
					}
					efree(lcname);

                    if (!ptr->scope) {
		                zend_error(E_WARNING, "Cannot find class %s", Z_STRVAL_PP(obj));
		                RETURN_FALSE;
                    }
                    ptr->object= NULL;
                }

	            if (zend_hash_find(&ptr->scope->function_table, Z_STRVAL_PP(method), Z_STRLEN_PP(method) + 1, (void**) &ptr->callable) == FAILURE) {
		            zend_error(E_WARNING, "Given argument %s::%s is not callable", ptr->scope->name, Z_STRVAL_PP(method));
		            RETURN_FALSE;
	            }
            } else {
		        zend_error(E_WARNING, "Given argument is not callable array");
		        RETURN_FALSE;
            }            
            break;
        }
        
        default: {
		    zend_error(E_WARNING, "Given argument is not callable (expected: string, [$object, $method] or [$classname, $method])");
		    RETURN_FALSE;
        }
    }

fprintf(stderr, "---> Starting thread %s (%s)\n", ptr->name, ptr->callable->common.function_name);

    ptr->context= tsrm_new_interpreter_context();
    
    /* Pass arguments across scope */
    if (arguments) {
        void *prior_context= tsrm_set_interpreter_context(ptr->context); 
        HashPosition pos;
        zval **tmp;
        zval *param;
        int i;
        
        ptr->argument_count= zend_hash_num_elements(Z_ARRVAL_P(arguments));
        ptr->arguments= safe_emalloc(sizeof(zval**),  ptr->argument_count, 0);
	    for (zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(arguments), &pos), i = 0;
		    (zend_hash_get_current_data_ex(Z_ARRVAL_P(arguments), (void**)&tmp, &pos) == SUCCESS) && (i < ptr->argument_count);
		    zend_hash_move_forward_ex(Z_ARRVAL_P(arguments), &pos), i++) {

		    ptr->arguments[i] = emalloc(sizeof(zval*));
            MAKE_STD_ZVAL(*ptr->arguments[i]);
		    **ptr->arguments[i]= **tmp;
            zval_copy_ctor(*ptr->arguments[i]);
            (*ptr->arguments[i])->refcount= 1;
            (*ptr->arguments[i])->is_ref= 0;
            
	    }
        tsrm_set_interpreter_context(prior_context); 
    } else {
        ptr->arguments= NULL;
    }

    /* Create thread */
    r= pthread_create(ptr->thread, NULL, php_threads_run, (void*) ptr);
	if (r) {
        tsrm_free_interpreter_context(ptr->context);
		zend_error(E_WARNING, "Could not create thread: pthread_create() returns %d", r);
		RETURN_FALSE;
	}

fprintf(stderr, "     Started thread %p %s (%s)\n", ptr->thread, ptr->name, ptr->callable->common.function_name);
    
	RETURN_TRUE;
}
/* }}} */


/* {{{ proto mixed thread_join(resource thread)
   Joins the specified thread */
PHP_FUNCTION(thread_join)
{
	zval **resource;
    php_thread_ptr* ptr= NULL;
	void *status;
    int r;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", resource) == FAILURE) {
		return;
	}
	ZEND_FETCH_RESOURCE(ptr, php_thread_ptr *, resource, -1, "Thread", le_threads);

fprintf(stderr, "---> Joining thread %s\n", ptr->name);

	r= pthread_join(*ptr->thread, (void **)&status);
	if (r) {
		zend_error(E_WARNING, "Could not join thread: pthread_join() returns %d", r);
		RETURN_FALSE;
	}

    /* Return copy of whatever thread returns */
	if (status) {
		*return_value = *(zval*)status;
		zval_copy_ctor(return_value);
		INIT_PZVAL(return_value);
		zval_ptr_dtor((zval*)&status);
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
