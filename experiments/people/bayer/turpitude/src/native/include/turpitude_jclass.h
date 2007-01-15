#ifndef __TURPITUDE_JCLASS_H__
#define __TURPITUDE_JCLASS_H__

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

typedef struct turpitude_javaclass_object {
    zend_object     std;
    jclass          java_class;
};

void make_turpitude_jclass();
void make_turpitude_jclass_instance(char* classname, zval* dest);

#endif
