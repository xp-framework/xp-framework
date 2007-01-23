#ifndef __TURPITUDE_ENV_H__
#define __TURPITUDE_ENV_H__

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

typedef struct turpitude_environment_object {
    zend_object     std;
    jobject         script_context;
    JNIEnv*         java_env;
};

void make_turpitude_environment();


#endif
