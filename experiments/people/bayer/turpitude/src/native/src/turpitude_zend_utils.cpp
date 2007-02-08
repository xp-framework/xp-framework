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

void print_HashTable(HashTable* ht) {
    HashPosition pos;
    zval** hashval;

    printf("HashTable: \n");
    // reset and iterate on HashTable
    zend_hash_internal_pointer_reset_ex(ht, &pos);
    while (zend_hash_get_current_data_ex(ht, (void **) &hashval, &pos) == SUCCESS) {
        char* key_name;
        ulong num_key;
        uint  str_len;

        switch (zend_hash_get_current_key_ex(ht, &key_name, &str_len, &num_key, 0, &pos)) {
            case HASH_KEY_IS_STRING:
                printf("key: %s\n", key_name);
            break;
            case HASH_KEY_IS_LONG:
                printf("key: %d\n", num_key);
            break;
        }

        zend_hash_move_forward_ex(ht, &pos);
    }
}

void print_zval(zval* val) {
    switch (Z_TYPE_P(val)) {
        case IS_LONG: 
            printf("zval: IS_LONG %d\n",val->value.lval);
            break; 
        case IS_DOUBLE:
            printf("zval: IS_DOUBLE %f\n",val->value.dval);
            break; 
        case IS_BOOL: 
            printf("zval: IS_BOOL %b\n",(val->value.lval)?true:false);
            break; 
        case IS_ARRAY: 
            printf("zval: IS_ARRAY \n");
            break; 
        case IS_OBJECT: 
            printf("zval: IS_OBJECT \n");
            break; 
        case IS_CONSTANT:
        case IS_STRING: 
            printf("zval: IS_STRING %s \n", val->value.str.val);
            break; 
        default:
            printf("zval: UNKNOWN \n");
    }
}

