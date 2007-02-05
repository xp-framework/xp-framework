#ifndef __TURPITUDE_ZEND_UTILS_H__
#define __TURPITUDE_ZEND_UTILS_H__

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

static void make_lambda(zend_internal_function* f, void (*handler)(INTERNAL_FUNCTION_PARAMETERS)) {
    memset(f, 0, sizeof*f);
    f->type = ZEND_INTERNAL_FUNCTION;
    f->handler = handler;
}

extern JNIEnv* turpitude_jenv;
extern jobject turpitude_current_script_context;
zval* make_php_class_instance(JNIEnv* env, char* classname);
void print_HashTable(HashTable* ht);

#endif
