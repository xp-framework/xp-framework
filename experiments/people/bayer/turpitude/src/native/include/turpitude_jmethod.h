#ifndef __TURPITUDE_JMETHOD_H__
#define __TURPITUDE_JMETHOD_H__

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

typedef struct turpitude_javamethod_object {
    zend_object     std;
    jclass          java_class;
    jmethodID       java_method; 
};

void make_turpitude_jmethod();
void make_turpitude_jmethod_instance(jclass cls, char* name, char* sig, zval* dest);

#endif
