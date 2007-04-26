#include <net_xp_framework_turpitude_PHPScriptEngine.h>
#include <Turpitude.h>

//typedef struct {
//    JNIEnv* env;
//    jobject object;
//} turpitude_context;


JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_startUp(JNIEnv* env, jobject jc) {
    TSRMLS_FETCH();

    //make sure php info outputs plain text
    turpitude_sapi_module.phpinfo_as_text= 1;

    //start up sapi
    sapi_startup(&turpitude_sapi_module);
    //start up php backend, check for errors
    if (SUCCESS != php_module_startup(&turpitude_sapi_module, NULL, 0))
        java_throw(env, "javax/script/ScriptException", "Cannot startup SAPI module");

    //call ini callback
    jclass cls = env->FindClass("net/xp_framework/turpitude/PHPScriptEngine");
    jmethodID mid = env->GetMethodID(cls, "setIniParams", "()V");
    env->CallObjectMethod(jc, mid);

    // Initialize request 
    if (SUCCESS != php_request_startup(TSRMLS_C)) 
        java_throw(env, "javax/script/ScriptException", "unable to start up request - php_request_startup()");

    // initialize Turpitude classes
    zend_first_try {
        make_turpitude_environment();
        make_turpitude_jclass();
        make_turpitude_jmethod();
        make_turpitude_jobject();
        make_turpitude_jarray();
        make_turpitude_jarray_iterator();
    } zend_catch {
        java_throw(env, "net/xp_framework/turpitude/PHPScriptException", LastError.data());
    } zend_end_try();
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


        set_zend_globals();
/*
        zend_error_cb= turpitude_error_cb;
        zend_uv.html_errors= 0;
        CG(in_compilation)= 0;
        CG(interactive)= 0;
        EG(uninitialized_zval_ptr)= NULL;
        EG(error_reporting)= E_ALL;

        INIT_ZVAL(EG(uninitialized_zval));
        EG(uninitialized_zval).refcount++;
        INIT_ZVAL(EG(error_zval));
        EG(uninitialized_zval_ptr)=&EG(uninitialized_zval);
        EG(error_zval_ptr)=&EG(error_zval);
*/
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

JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPScriptEngine_setIniParam(JNIEnv *env, jobject thiz, jstring key, jstring val) {
    TSRMLS_FETCH();
    //retrieve c strings and lengths, from java string, cast to char* (I won't change them, I promise...)
    char* keystr = (char*)env->GetStringUTFChars(key, 0);
    int keylen = strlen(keystr)+1;
    char* valstr = (char*)env->GetStringUTFChars(val, 0);
    int vallen = strlen(valstr);

    zend_alter_ini_entry(keystr, keylen, valstr, vallen, PHP_INI_ALL, PHP_INI_STAGE_ACTIVATE);

    env->ReleaseStringUTFChars(key, keystr);
    env->ReleaseStringUTFChars(val, valstr);
}
