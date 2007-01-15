#ifndef __TURPITUDE_JOBJ_H__
#define __TURPITUDE_JOBJ_H__

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

typedef struct turpitude_javaobject_object {
    zend_object     std;
    jclass          java_class;
    jobject         java_object; 
};

void make_turpitude_jobject();
void make_turpitude_jobject_instance(jclass cls, zval* turpcls, jobject obj, zval* dest);

#endif
