#include <net_xp_framework_turpitude_PHPCompiledScript.h>
#include <Turpitude.h>


JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPCompiledScript_execute(JNIEnv* env, jobject self, jobject ctx) {
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

        // generate turpitude classes
        make_turpitude_environment(env, ctx);
        make_turpitude_jclass();
        make_turpitude_jmethod();

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

    jclass retcls = env->FindClass("java/lang/Boolean");
    jmethodID retmid = env->GetMethodID(retcls, "<init>", "(Z)V");
    return zval_to_jobject(env, retval_ptr);
    return env->NewObject(retcls, retmid, true);
}


