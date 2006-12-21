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
        java_throw(env, "javax/script/ScriptException", "Cannot startup SAPI module");

    // Initialize request 
    if (SUCCESS != php_request_startup(TSRMLS_C)) 
        java_throw(env, "javax/script/ScriptException", "unable to start up request - php_request_startup()");

}

JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_shutDown(JNIEnv *, jobject) {
    TSRMLS_FETCH();

    // Shutdown PHP module 
    php_request_shutdown((void *) 0);
    php_module_shutdown(TSRMLS_C);
    sapi_shutdown();
}

JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_compilePHP(JNIEnv* env, jobject obj, jstring src) {
    TSRMLS_FETCH();

    // return value
    zend_op_array* compiled_op_array= NULL;

    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        zend_error_cb= turpitude_error_cb;
        zend_uv.html_errors= 0;
        CG(in_compilation)= 0;
        CG(interactive)= 0;
        EG(uninitialized_zval_ptr)= NULL;
        EG(error_reporting)= E_ALL;

        LastError = "";
        const char* str= env->GetStringUTFChars(src, 0); 
        {

            zval eval;
            char* eval_desc = zend_make_compiled_string_description("jni compile()'d code" TSRMLS_CC);

            eval.value.str.val= (char*) emalloc(strlen(str)+ 1);
            eval.value.str.len= strlen(str);
            strncpy(eval.value.str.val, str, eval.value.str.len);
            eval.value.str.val[eval.value.str.len]= '\0';
            eval.type= IS_STRING;

            //printf("Code --> |%s| <--\n", eval.value.str.val);
            compiled_op_array= compile_string(&eval, eval_desc TSRMLS_CC);

            efree(eval_desc);
            zval_dtor(&eval);
        }
        // make sure memory is freed properly
        env->ReleaseStringUTFChars(src, str);

        zend_llist_destroy(&global_vars);
    } zend_catch {
        java_throw(env, "net/xp_framework/turpitude/PHPCompileException", LastError.data());
    } zend_end_try();

    /* Check if compilation worked */
    if (!compiled_op_array) {
        java_throw(env, "net/xp_framework/turpitude/PHPCompileException", LastError.data());
    }

    // Create PHPCompiledScript object and return it 
    jclass cls = env->FindClass("net/xp_framework/turpitude/PHPCompiledScript");
    if (NULL == cls) 
        java_throw(env, "javax/script/ScriptException", "unable to find class net/xp_framework/turpitude/PHPCompiledScript");

    jobject compiledscript = env->AllocObject(cls);
    if (NULL == compiledscript) 
        java_throw(env, "javax/script/ScriptException", "unable to allocate object (net/xp_framework/turpitude/PHPCompiledScript)");

    jfieldID oparrayField = env->GetFieldID(cls, "ZendOpArrayptr", "Ljava/nio/ByteBuffer;");
    if (NULL == oparrayField) 
        java_throw(env, "javax/script/ScriptException", "unable find fieldID (ZendOpArrayptr)");


    env->SetObjectField(
        compiledscript, 
        oparrayField,
        env->NewDirectByteBuffer(compiled_op_array, sizeof(compiled_op_array))
    );

    return compiledscript;
}

