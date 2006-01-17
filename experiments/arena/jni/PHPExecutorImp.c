/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>
#include "PHPExecutor.h"
#include "executor_sapi.h"

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
