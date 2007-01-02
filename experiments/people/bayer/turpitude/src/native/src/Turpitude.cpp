#include <Turpitude.h>

jobject zval_to_jobject(JNIEnv* env, zval* val) {
    if (NULL == val)
        return NULL;

    TSRMLS_FETCH();

    jclass cls = NULL;
    jobject obj = NULL;
    jmethodID mid = NULL;

    switch (Z_TYPE_P(val)) {
        case IS_LONG:
            //printf("IS_LONG\n");
            cls = env->FindClass("java/lang/Long");
            mid = env->GetMethodID(cls, "<init>", "(J)V");
            obj = env->NewObject(cls, mid, val->value.lval);
            break;
        case IS_DOUBLE:
            //printf("IS_DOUBLE\n");
            cls = env->FindClass("java/lang/Double");
            mid = env->GetMethodID(cls, "<init>", "(D)V");
            obj = env->NewObject(cls, mid, val->value.dval);
            break;
        case IS_BOOL:
            //printf("IS_BOOL\n");
            cls = env->FindClass("java/lang/Boolean");
            mid = env->GetMethodID(cls, "<init>", "(Z)V");
            obj = env->NewObject(cls, mid, (val->value.lval)?true:false);
            break;
        case IS_ARRAY:
            printf("IS_ARRAY\n");
            break;
        case IS_OBJECT:
            //printf("IS_OBJECT\n");
            zend_class_entry* ce = Z_OBJCE_P(val);
            printf("Class: %s\n", ce->name);
            zend_object_value* zo = &(val->value.obj);
            printf("Handle: %d\n", zo->handle);
            HashTable* props = Z_OBJ_HT_P(val)->get_properties(val TSRMLS_CC);
            HashPosition pos;
            zend_hash_internal_pointer_reset_ex(props, &pos);
            zval** prop;
            while (zend_hash_get_current_data_ex(props, (void **) &prop, &pos) == SUCCESS) {
                char  *prop_name;
                uint  prop_name_size;
                ulong index;
                if (zend_hash_get_current_key_ex(props, &prop_name, &prop_name_size, &index, 1, &pos) == HASH_KEY_IS_STRING) {
                    if (prop_name_size && prop_name[0]) {
                        printf("propname: %s\n", prop_name);
                    }
                    efree(prop_name);
                }
                zend_hash_move_forward_ex(props, &pos);
            }
            /*
            zend_hash_get_current_data_ex(props, (void **) &prop, &pos);
            char  *prop_name;
            uint  prop_name_size;
            ulong index;
            if (zend_hash_get_current_key_ex(props, &prop_name, &prop_name_size, &index, 1, &pos) == HASH_KEY_IS_STRING) {
                if (prop_name_size && prop_name[0]) {
                        printf("propname: %s\n", prop_name);
                    if (!zend_hash_quick_exists(&ce->properties_info, prop_name, prop_name_size, zend_get_hash_value(prop_name, prop_name_size))) {
                printf("hierher\n");
                    }
                }
            }
            */

            break;
        case IS_CONSTANT:
        case IS_STRING:
            printf("IS_STRING or IS_CONSTANT\n");
            obj = env->NewStringUTF(val->value.str.val);
            break;
        default:
            // probably null
            obj = NULL;
    }

    return obj;
}

