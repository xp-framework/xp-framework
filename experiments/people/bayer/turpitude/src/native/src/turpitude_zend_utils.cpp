#include <turpitude_zend_utils.h>

JNIEnv* turpitude_jenv;
jobject turpitude_current_script_context;

zval* make_php_class_instance(JNIEnv* env, char* classname) {
    zend_class_entry **pce;

    if (zend_lookup_class(classname, strlen(classname), &pce TSRMLS_CC) == FAILURE) {
        php_error(E_ERROR, "unable to find class %s", classname);
        return NULL;
    }

    zval* dest; 
    ALLOC_ZVAL(dest);
  
    // instantiate object
    Z_TYPE_P(dest) = IS_OBJECT;
    object_init_ex(dest, *pce);
    dest->refcount = 1;
    dest->is_ref = 1;

    return dest;
}


