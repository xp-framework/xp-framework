#include <net_xp_framework_turpitude_PHPCompiledScript.h>
#include <Turpitude.h>

zend_op_array* getOpArrayPtr(JNIEnv* env, jobject self) {
    // find class
    jclass myclass = env->GetObjectClass(self);
    if (NULL == myclass)
        java_throw(env, "javax/script/ScriptException", "unable to find class via GetObjectClass");
    // find ZendOpArrayptr field if
    jfieldID oparrayField = env->GetFieldID(myclass, "ZendOpArrayptr", "Ljava/nio/ByteBuffer;");
    if (NULL == oparrayField) 
        java_throw(env, "javax/script/ScriptException", "unable find fieldID (ZendOpArrayptr)");
    // retrieve pointer to op_array
    zend_op_array* compiled_op_array = NULL;
    compiled_op_array = (zend_op_array*)(
        env->GetDirectBufferAddress(env->GetObjectField(self, oparrayField))
    );
    if (NULL == compiled_op_array)
        java_throw(env, "javax/script/ScriptException", "ZendOpArrayptr empty");

    return compiled_op_array;
}

zval* getZvalPtr(JNIEnv* env, jobject self) {
    // find class
    jclass myclass = env->GetObjectClass(self);
    if (NULL == myclass)
        java_throw(env, "javax/script/ScriptException", "unable to find class via GetObjectClass");
    // find ZValptr field 
    jfieldID ZValptrField = env->GetFieldID(myclass, "ZValptr", "Ljava/nio/ByteBuffer;");
    if (NULL == ZValptrField) 
        java_throw(env, "javax/script/ScriptException", "unable find fieldID (ZValptr)");
    // retrieve pointer to op_array
    zval* retval = NULL;
    retval = (zval*)(
        env->GetDirectBufferAddress(env->GetObjectField(self, ZValptrField))
    );
    if (NULL == retval)
        java_throw(env, "javax/script/ScriptException", "ZValptr empty");

    return retval;
}


JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPCompiledScript_execute(JNIEnv* env, jobject self, jobject ctx) {
    zend_op_array* compiled_op_array = getOpArrayPtr(env, self);

    zval* retval_ptr = NULL;
    // execute!
    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        zend_fcall_info_cache fci_cache;
        zend_fcall_info fci;
         
        memset(&fci, 0, sizeof(fci));
        memset(&fci_cache, 0, sizeof(fci_cache));
         
        fci.size = sizeof(fci);
        fci.function_table = CG(function_table);

        fci.retval_ptr_ptr = &retval_ptr;
        fci.no_separation = 1;
        fci.param_count = 0;

        fci_cache.initialized = 1;
        fci_cache.function_handler = (zend_function*)compiled_op_array;
        compiled_op_array->type = ZEND_USER_FUNCTION;

        // initialize Turpitude globals
        turpitude_jenv = env;
        turpitude_current_script_context = ctx;

        // We could inject parameters to be retrieved by func_getargs() here...
        //zval** param = ;
        //fci.param_count = ;
        //fci.params = &param;

        // use zend_call_function to execute the script
        zend_call_function(&fci, &fci_cache TSRMLS_CC);
       
        zend_llist_destroy(&global_vars);
    } zend_catch {
        if (ErrorCBCalled)
            java_throw(env, "net/xp_framework/turpitude/PHPEvalException", LastError.data());
    } zend_end_try();

    return zval_to_jobject(env, retval_ptr);
}

JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPCompiledScript_nativeInvokeFunction(JNIEnv* env, jobject thiz, jstring name, jobjectArray args) {
    zend_op_array* compiled_op_array = getOpArrayPtr(env, thiz);

    const char* methodName= env->GetStringUTFChars(name, 0);

    zval* retval_ptr = NULL;
    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        EG(return_value_ptr_ptr)= &retval_ptr;
        EG(active_op_array)= compiled_op_array;

        zval function;
        ZVAL_STRING(&function, estrdup(methodName), 0);

        jint arg_count= env->GetArrayLength(args);
        zval ***params= (zval ***)safe_emalloc(arg_count, sizeof(zval **), 0);

        for (jint i= 0; i < arg_count; i++) {
            params[i]= (zval**)emalloc(sizeof(zval **));
            
            ALLOC_ZVAL(*(params[i]));
            jobject_to_zval(env, env->GetObjectArrayElement(args, i), *params[i]);
            INIT_PZVAL(*(params[i]));
        }
        
        if (FAILURE == call_user_function_ex(
            CG(function_table), 
            NULL, 
            &function, 
            &retval_ptr, 
            arg_count, 
            params, 
            1, 
            NULL TSRMLS_CC
        )) {
            php_error(E_ERROR, "call to %s failed", methodName);
        }

        zval_dtor(&function);
        efree(params);

        EG(active_op_array)= NULL;

        zend_llist_destroy(&global_vars);
    } zend_catch {
        if (ErrorCBCalled)
            java_throw(env, "net/xp_framework/turpitude/PHPEvalException", LastError.data());
    } zend_end_try();

    env->ReleaseStringUTFChars(name, methodName);

    return zval_to_jobject(env, retval_ptr);
}

JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPCompiledScript_nativeInvokeMethod(JNIEnv* env, jobject thiz, jobject phpobj, jstring name, jobjectArray args) {
    zend_op_array* compiled_op_array = getOpArrayPtr(env, thiz);
    zval* objectptr = getZvalPtr(env, phpobj);

    const char* methodName= env->GetStringUTFChars(name, 0);

    zval* retval_ptr = NULL;
    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

        EG(return_value_ptr_ptr)= &retval_ptr;
        EG(active_op_array)= compiled_op_array;

        jint arg_count= env->GetArrayLength(args);
        zval ***params= (zval ***)safe_emalloc(arg_count, sizeof(zval **), 0);

        for (jint i= 0; i < arg_count; i++) {
            params[i]= (zval**)emalloc(sizeof(zval **));
            
            ALLOC_ZVAL(*(params[i]));
            jobject_to_zval(env, env->GetObjectArrayElement(args, i), *params[i]);
            INIT_PZVAL(*(params[i]));
        }
       
        zval function;
        ZVAL_STRING(&function, estrdup(methodName), 0);

        if (FAILURE == call_user_function_ex(
            CG(function_table), 
            &objectptr, 
            &function, 
            &retval_ptr, 
            arg_count, 
            params, 
            1, 
            NULL TSRMLS_CC
        )) {
            php_error(E_ERROR, "call to %s failed", methodName);
        }

        zval_dtor(&function);
        efree(params);

        EG(active_op_array)= NULL;

        zend_llist_destroy(&global_vars);
    } zend_catch {
        if (ErrorCBCalled)
            java_throw(env, "net/xp_framework/turpitude/PHPEvalException", LastError.data());
    } zend_end_try();

    env->ReleaseStringUTFChars(name, methodName);

    return zval_to_jobject(env, retval_ptr);
}


