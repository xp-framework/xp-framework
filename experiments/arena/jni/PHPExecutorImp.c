/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>
#include "PHPExecutor.h"
#include <stdio.h>
#include <main/php.h>
#include <main/SAPI.h>
#include <main/php_main.h>
#include <main/php_variables.h>
#include <main/php_ini.h>
#include <zend_ini.h>
#include <zend_errors.h>
#include <zend_compile.h>
#include <zend_execute.h>
#include "ext/standard/php_var.h"

/* {{{ PHP module functions */
static char* phpexecutor_read_cookies(TSRMLS_D)
{
    return NULL;
}

static int phpexecutor_deactivate(TSRMLS_D)
{
    fflush(stdout);
    return SUCCESS;
}

static int phpexecutor_ub_write(const char *str, uint str_length TSRMLS_DC)
{
    /* DEBUG fprintf(stderr, "[SAPI] ub_write (%d)'%s'\n", str_length, str); */
    printf(str);
    return str_length;
}

static void phpexecutor_flush(void *server_context)
{
    fprintf(stderr, "[SAPI] flush\n");
}

static int phpexecutor_send_headers(sapi_headers_struct *sapi_headers TSRMLS_DC)
{
	return SAPI_HEADER_SENT_SUCCESSFULLY;
}

static void phpexecutor_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC)
{
}

static void phpexecutor_log_message(char *message)
{
    fprintf(stderr, message);
}

static void phpexecutor_register_variables(zval *track_vars_array TSRMLS_DC)
{
    php_import_environment_variables(track_vars_array TSRMLS_CC);
}

static int phpexecutor_startup(sapi_module_struct *sapi_module)
{
    return php_module_startup(sapi_module, NULL, 0);
}

static void phpexecutor_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
    char *buffer;
    int buffer_len;
    TSRMLS_FETCH();
    if (!(EG(error_reporting) & type)) return;
  
    buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
    fprintf(stderr, "*** Error #%d on line %d of %s\n    %s\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);
    efree(buffer);
    
	switch (type) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_USER_ERROR:
            zend_bailout();
            break;
    }
}
/* }}} */

/* {{{ PHP SAPI registry */
static sapi_module_struct phpexecutor_sapi_module = {
    "phpexecutor",                            /* name */
    "Java PHP Executor",                      /* pretty name */

    phpexecutor_startup,                      /* startup */
    php_module_shutdown_wrapper,              /* shutdown */

    NULL,                                     /* activate */
    phpexecutor_deactivate,                   /* deactivate */

    phpexecutor_ub_write,                     /* unbuffered write */
    phpexecutor_flush,                        /* flush */
    NULL,                                     /* get uid */
    NULL,                                     /* getenv */

    php_error,                                /* error handler */

    NULL,                                     /* header handler */
    phpexecutor_send_headers,                 /* send headers handler */
    phpexecutor_send_header,                  /* send header handler */

    NULL,                                     /* read POST data */
    phpexecutor_read_cookies,                 /* read Cookies */

    phpexecutor_register_variables,           /* register server variables */
    phpexecutor_log_message,                  /* Log message */

	NULL,							          /* Block interruptions */
	NULL,							          /* Unblock interruptions */

    STANDARD_SAPI_MODULE_PROPERTIES
};
/* }}} */

static void throw(JNIEnv* env, const char *classname, const char* message) {
    jclass exception = (*env)->FindClass(env, classname);
    if (exception != 0) {
        (*env)->ThrowNew(env, exception, message);
    }
}

typedef struct {
	JNIEnv *env;
	jobject object;
} executor_context;


/* {{{ void startUp() */
JNIEXPORT void JNICALL Java_PHPExecutor_startUp(JNIEnv *env, jobject object) {

    /* Startup PHP module */
    phpexecutor_sapi_module.phpinfo_as_text= 1;
    sapi_startup(&phpexecutor_sapi_module);
    if (SUCCESS != php_module_startup(&phpexecutor_sapi_module, NULL, 0)) {
        throw(env, "java/lang/IllegalArgumentException", "Cannot startup SAPI module");
    }
}
/* }}} */

/* {{{ void shutDown() */
JNIEXPORT void JNICALL Java_PHPExecutor_shutDown(JNIEnv *env, jobject object) {
	TSRMLS_FETCH();

    /* Shutdown PHP module */
    php_module_shutdown(TSRMLS_C);
    sapi_shutdown();
}
/* }}} */

/* {{{ Object eval(String source) */
JNIEXPORT jobject JNICALL Java_PHPExecutor_eval(JNIEnv* env, jobject object, jstring source) {
	TSRMLS_FETCH();

    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        SG(server_context)= emalloc(sizeof(executor_context));
        ((executor_context*)SG(server_context))->env= env;
        ((executor_context*)SG(server_context))->object= object;

        zend_error_cb= phpexecutor_error_cb;
		zend_uv.html_errors= 0;
		CG(in_compilation)= 0;
        CG(interactive)= 0;
		EG(uninitialized_zval_ptr)= NULL;
		EG(error_reporting)= E_ALL;

        /* Initialize request */
        if (SUCCESS != php_request_startup(TSRMLS_C)) {
            fprintf(stderr, "Cannot startup request\n");
            return;
        }
        
        /* Execute */
        const char *str= (*env)->GetStringUTFChars(env, source, 0);
        {
            char *eval= (char*) emalloc(strlen(str)+ 1);
            strncpy(eval, str, strlen(str));
            eval[strlen(str)]= '\0';

            if (FAILURE == zend_eval_string(eval, NULL, "(jni)" TSRMLS_CC)) {
                throw(env, "java/lang/IllegalArgumentException", "zend_eval_string()");
            }
            efree(eval);
        }
        (*env)->ReleaseStringUTFChars(env, source, str);

        /* Shutdown request */
		efree(SG(server_context));
		SG(server_context)= 0;
        
        zend_llist_destroy(&global_vars);
        php_request_shutdown((void *) 0);
    } zend_catch {
        throw(env, "java/lang/IllegalArgumentException", "Bailout");
    } zend_end_try();

    return;
}
/* }}} */
