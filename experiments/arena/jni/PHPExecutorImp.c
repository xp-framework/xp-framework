/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>
#include "PHPExecutor.h"
#include "executor_sapi.h"

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

/* {{{ CompiledScript compile(String source) */
JNIEXPORT jobject JNICALL Java_PHPExecutor_compile(JNIEnv *env, jobject object, jstring source) {
	TSRMLS_FETCH();

    zend_op_array *compiled_op_array= NULL;

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
            zval eval;
            char *eval_desc = zend_make_compiled_string_description("jni compile()'d code" TSRMLS_CC);

            eval.value.str.val= (char*) emalloc(strlen(str)+ 1);
            eval.value.str.len= strlen(str);
            strncpy(eval.value.str.val, str, eval.value.str.len);
            eval.value.str.val[eval.value.str.len]= '\0';
            eval.type= IS_STRING;
            
            compiled_op_array= compile_string(&eval, eval_desc TSRMLS_CC);
  
            efree(eval_desc);
            zval_dtor(&eval);

            /* DEBUG printf("PHPExecutor.compile('%s') op_array= %p\n", str, compiled_op_array); */
        }
        (*env)->ReleaseStringUTFChars(env, source, str);


        /* Shutdown request */
		efree(SG(server_context));
		SG(server_context)= 0;
        
        zend_llist_destroy(&global_vars);
    } zend_catch {
        throw(env, "java/lang/IllegalArgumentException", "Bailout");
    } zend_end_try();

    /* Check if compilation worked */
    if (!compiled_op_array) {
        throw(env, "java/lang/IllegalArgumentException", "Compile error");
        return;
    }

    /* Create CompiledString object and return it */
    jclass compiledScriptClass= (*env)->FindClass(env, "CompiledScript");
    jobject compiledScriptObject= (*env)->AllocObject(env, compiledScriptClass);

    jfieldID oparrayField= (*env)->GetFieldID(env, compiledScriptClass, "oparrayptr", "Ljava/nio/ByteBuffer;");
    (*env)->SetObjectField(
        env, 
        compiledScriptObject, 
        oparrayField, 
        (*env)->NewDirectByteBuffer(env, compiled_op_array, sizeof(compiled_op_array)) 
    );

    return compiledScriptObject;
} 

/* {{{ Object eval(String source) */
JNIEXPORT jobject JNICALL Java_PHPExecutor_eval(JNIEnv *env, jobject object, jstring source) {
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
