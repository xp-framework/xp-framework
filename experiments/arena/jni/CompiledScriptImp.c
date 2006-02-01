/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>
#include "CompiledScript.h"
#include "executor_sapi.h"

void JObjectToZval(JNIEnv *env, jobject object, zval** rval) {
    if (object == NULL) {
        ZVAL_NULL(*rval);
    } else if ((*env)->IsInstanceOf(env, object, (*env)->FindClass(env, "java/lang/String"))) {
        const char* stringValue= (*env)->GetStringUTFChars(env, object, 0);
        ZVAL_STRING(*rval, (char *)stringValue, 1);
        (*env)->ReleaseStringUTFChars(env, object, stringValue);
    } else {
        printf("Unknown type");
        /* ZVAL_NULL(*val); */
    }

    INIT_PZVAL(*rval);
}

/* {{{ CompiledScript compile(String source) */
JNIEXPORT jobject JNICALL Java_CompiledScript_call(JNIEnv *env, jobject object, jobject self, jstring method, jobjectArray args) {
	TSRMLS_FETCH();
    
    const char *methodName= (*env)->GetStringUTFChars(env, method, 0);
    jclass cls = (*env)->GetObjectClass(env, object);
    jfieldID oparrayField = (*env)->GetFieldID(env, cls, "oparrayptr", "Ljava/nio/ByteBuffer;");
    
    zend_op_array *compiled_op_array= (zend_op_array*)((*env)->GetDirectBufferAddress(
        env, 
        (*env)->GetObjectField(env, object, oparrayField)
    ));
    
    /* DEBUG printf("CompiledScript.call({object}, %s, args[]) op_array = %p\n", methodName, compiled_op_array); */
    
    if (compiled_op_array) {
        zend_first_try {
            zend_llist global_vars;
            zend_llist_init(&global_vars, sizeof(char *), NULL, 0);

            zval *local_retval_ptr= NULL;

            EG(return_value_ptr_ptr)= &local_retval_ptr;
		    EG(active_op_array)= compiled_op_array;
		    EG(no_extensions)= 1;

            jint arg_count= (*env)->GetArrayLength(env, args);
            zval function;
            
            function.value.str.val= estrdup(methodName);
            function.value.str.len= strlen(methodName);
            function.type= IS_STRING;
            
            zval *retval_ptr= NULL;
            zval ***params= (zval ***)safe_emalloc(arg_count, sizeof(zval **), 0);
            
            jint i;
            for (i= 0; i < arg_count; i++) {
                params[i]= (zval**)emalloc(sizeof(zval **));
                
                ALLOC_ZVAL(*(params[i]));
                JObjectToZval(env, (*env)->GetObjectArrayElement(env, args, i), params[i]);
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
                printf("*** Calling %s() failed\n", methodName);
            }

            zval_dtor(&function);
            efree(params);

            EG(no_extensions)= 0;
		    destroy_op_array(compiled_op_array);
            EG(active_op_array)= NULL;

            zend_llist_destroy(&global_vars);
        } zend_catch {
            throw(env, "java/lang/IllegalArgumentException", "Bailout");
        } zend_end_try();
    }
    
    (*env)->ReleaseStringUTFChars(env, method, methodName);
    
    return;
}
/* }}} */
