/* This file is part of the XP framework's experiment "JNI"
 *
 * $Id$
 */

#include <jni.h>
#include "CompiledScript.h"
#include "executor_sapi.h"

/* {{{ CompiledScript compile(String source) */
JNIEXPORT jobject JNICALL Java_CompiledScript_call(JNIEnv *env, jobject object, jobject self, jstring method, jobjectArray args) {
	TSRMLS_FETCH();
    
    const char *methodName= (*env)->GetStringUTFChars(env, method, 0);
    
    printf("CompiledScript.call({object}, %s, args[])\n", methodName);
    
    (*env)->ReleaseStringUTFChars(env, method, methodName);
    
    return;
}
/* }}} */
