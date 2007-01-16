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

enum turpitude_javamethod_return_type {
    JAVA_VOID       = 0,
    JAVA_OBJECT     = 1,
    JAVA_BOOLEAN    = 2,
    JAVA_BYTE       = 3,
    JAVA_CHAR       = 4,
    JAVA_SHORT      = 5,
    JAVA_INT        = 6,
    JAVA_LONG       = 7,
    JAVA_FLOAT      = 8,
    JAVA_DOUBLE     = 9
};

typedef struct turpitude_javamethod_object {
    zend_object                         std;
    jclass                              java_class;
    jmethodID                           java_method; 
    turpitude_javamethod_return_type    return_type;
};

void make_turpitude_jmethod();
void make_turpitude_jmethod_instance(jclass cls, char* name, char* sig, zval* dest);

#endif
