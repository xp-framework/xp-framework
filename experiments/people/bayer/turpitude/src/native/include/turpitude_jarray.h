#ifndef __TURPITUDE_JARRAY_H__
#define __TURPITUDE_JARRAY_H__

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

typedef struct turpitude_javaarray_object {
    zend_object         std;
    jarray              java_array; 
    turpitude_java_type type;
    int                 array_length;
};

void make_turpitude_jarray();
void make_turpitude_jarray_instance(jarray array, turpitude_java_type type, zval* dest);

#endif
