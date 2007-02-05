#ifndef __TURPITUDE_JARRAY_ITERATOR_H__
#define __TURPITUDE_JARRAY_ITERATOR_H__

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

typedef struct turpitude_javaarray_iterator_object {
    zend_object         std;
    jarray              java_array; 
};

void make_turpitude_jarray_iterator();
void make_turpitude_jarray_iterator_instance(jarray array, zval* dest);

#endif
