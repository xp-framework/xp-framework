#ifndef __TURPITUDE_H__
#define __TURPITUDE_H__

#include <main/php.h>
#include <main/SAPI.h>
#include <main/php_main.h>
#include <main/php_variables.h>
#include <main/php_ini.h>
#include <zend_ini.h>
#include <zend_errors.h>
#include <zend_compile.h>
#include <zend_execute.h>

#include <jni.h>
#include <string>
#include "turpitude_types.h"
#include "turpitude_sapi.h"
#include "turpitude_env.h"
#include "turpitude_jclass.h"
#include "turpitude_jmethod.h"
#include "turpitude_jobj.h"
#include "turpitude_jarray.h"
#include "turpitude_zend_utils.h"

/* utility functions */
static void java_throw(JNIEnv* env, const char* classname, const char* message) {
        jclass exception = env->FindClass(classname);
        if (exception != 0)
            env->ThrowNew(exception, message);
}

jobject zval_to_jobject(JNIEnv* env, zval* val);
jvalue zval_to_jvalue(JNIEnv* env, zval* val);
zval* jvalue_to_zval(JNIEnv* env, jvalue val, turpitude_java_type type, zval* dest);
void jobject_to_zval(JNIEnv* env, jobject obj, zval* retval);
void jarray_to_zval(JNIEnv* env, jobject obj, turpitude_java_type type, zval* retval);
jclass get_java_class(JNIEnv* env, jobject obj, char** dest);


#endif
