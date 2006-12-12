#include <net_xp_framework_turpitude_PHPScriptEngine.h>
#include <Turpitude.h>

typedef struct {
    JNIEnv* env;
    jobject object;
} turpitude_context;


JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_startUp(JNIEnv* env, jobject jc) {
    //make sure php info outputs plain text
    turpitude_sapi_module.phpinfo_as_text= 1;
    //start up sapi
    sapi_startup(&turpitude_sapi_module);
    //start up php backend, check for errors
    if (SUCCESS != php_module_startup(&turpitude_sapi_module, NULL, 0))
        java_throw(env, "java/lang/IllegalArgumentException", "Cannot startup SAPI module");
}

JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_shutDown(JNIEnv *, jobject) {
    TSRMLS_FETCH();

    // Shutdown PHP module 
    php_module_shutdown(TSRMLS_C);
    sapi_shutdown();
}

JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_evalPHP(JNIEnv* env, jobject obj, jstring src) {
    TSRMLS_FETCH();
    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        //SG(server_context)= emalloc(sizeof(turpitude_context));
        //((turpitude_context*)SG(server_context))->env= env;
        //((turpitude_context*)SG(server_context))->object= obj;

        zend_error_cb= turpitude_error_cb;
        zend_uv.html_errors= 0;
        CG(in_compilation)= 0;
        CG(interactive)= 0;
        EG(uninitialized_zval_ptr)= NULL;
        EG(error_reporting)= E_ALL;

        // Initialize request 
        if (SUCCESS != php_request_startup(TSRMLS_C)) {
            java_throw(env, "javax/script/ScriptException", "unable to start up request - php_request_startup()");
            return NULL;
        }

        // Execute 
        LastError = "";
        const char *str= env->GetStringUTFChars(src, 0); 
        {
            // copy string containing source
            char *eval= (char*) emalloc(strlen(str)+ 1);
            strncpy(eval, str, strlen(str));
            eval[strlen(str)]= '\0';

            //printf("Code --> |%s| <--\n", eval);
            if (FAILURE == zend_eval_string(eval, NULL, "(jni)" TSRMLS_CC)) {
                java_throw(env, "javax/script/ScriptException", "zend_eval_string()");
            }
            efree(eval);
        }
        // make sure memory is freed properly
        env->ReleaseStringUTFChars(src, str);
        //efree(SG(server_context));
        //SG(server_context)= 0;

        // Shutdown request
        zend_llist_destroy(&global_vars);
        php_request_shutdown((void *) 0);

    } zend_catch {
        java_throw(env, "javax/script/ScriptException", LastError.data());
    } zend_end_try();


    return NULL;
}
