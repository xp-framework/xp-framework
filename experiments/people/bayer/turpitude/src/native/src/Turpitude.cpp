#include <Turpitude.h>

/**
 * returns the jclass obj is an instance of
 * if dest != NULL it will point to the JNI-formatted classname
 */
jclass get_java_class(JNIEnv* env, jobject obj, char** dest) {
    // Class and Object classes
    jclass ocls = env->FindClass("java/lang/Object");
    jclass ccls = env->FindClass("java/lang/Class");

    // getClass and getName jmethodIDs
    jmethodID gcid = env->GetMethodID(ocls, "getClass", "()Ljava/lang/Class;");
    if (gcid == NULL) {
        java_throw(env, "java/lang/NoSuchMethodException", "unable to find method getClass on java/lang/Object.");
        return NULL;
    }
    jmethodID cnid = env->GetMethodID(ccls, "getName", "()Ljava/lang/String;");
    if (cnid == NULL) {
        java_throw(env, "java/lang/NoSuchMethodException", "unable to find method getName on java/lang/Class.");
        return NULL;
    }

    // call getClass 
    jobject clsobj = env->CallObjectMethod(obj, gcid);
    if (clsobj == NULL) {
        java_throw(env, "java/lang/ClassNotFoundException", "unable to find Class");
        return NULL;
    }
    // call getName on class
    jstring namestr = (jstring)env->CallObjectMethod(clsobj, cnid); 

    // copy classname string, replace '.' with '/'
    int str_len = env->GetStringLength(namestr)+1;
    char* classname = (char*)emalloc(env->GetStringLength(namestr)+1);
    strncpy(classname, env->GetStringUTFChars(namestr, false), str_len);
    char* cnp = classname;
    char c;
    while (c = *++cnp) if (c == '.') *cnp = '/';

    if (dest != NULL) *dest = classname;

    return (jclass)clsobj;
}

/**
 * converts a jvalue to a zval
 * uses dest if dest != null, creates a new zval otherwise
 */
zval* jvalue_to_zval(JNIEnv* env, jvalue val, turpitude_javamethod_return_type type, zval* dest) {
    // initialize return value
    zval* retval;
    if (dest != NULL)
        retval = dest;
    else
        MAKE_STD_ZVAL(retval);

    switch (type) {
        // long types
        case JAVA_LONG:
            ZVAL_LONG(retval, val.j);
            break;
        case JAVA_INT:
            ZVAL_LONG(retval, val.i);
            break;
        case JAVA_SHORT:
            ZVAL_LONG(retval, val.s);
            break;
        case JAVA_CHAR:
            ZVAL_LONG(retval, val.c);
            break;
        case JAVA_BYTE:
            ZVAL_LONG(retval, val.b);
            break;
        // boolean
        case JAVA_BOOLEAN:
            ZVAL_BOOL(retval, val.z);
            break;
        // real numbers
        case JAVA_FLOAT:
            ZVAL_DOUBLE(retval, val.f);
            break;
        case JAVA_DOUBLE:
            ZVAL_DOUBLE(retval, val.d);
            break;
        // objects
        case JAVA_OBJECT:
            char* classname;
            jclass cls = get_java_class(env, val.l, &classname);
            // ###### check for special objects
            // Strings
            if (strcmp(classname, "java/lang/String") == 0) {
                // copy string value
                int str_len = env->GetStringLength((jstring)val.l)+1;
                char* str_val = (char*)emalloc(str_len);
                strncpy(str_val, env->GetStringUTFChars((jstring)val.l, 0), str_len);
                ZVAL_STRING(retval, str_val, 0);
                break;
            }
            zval* turpcls;
            MAKE_STD_ZVAL(turpcls);
            make_turpitude_jclass_instance(cls, classname, turpcls);
            make_turpitude_jobject_instance(cls, turpcls, val.l, retval);
            break;
        default:
            // probably void
            ZVAL_NULL(retval);
    }

    return retval;
}

/**
 * copies the contents of a zval into a jvalue
 */
jvalue zval_to_jvalue(JNIEnv* env, zval* val) {
    jvalue ret;
    if (NULL == val) {
        ret.l = NULL;
        return ret;
    }

    switch (Z_TYPE_P(val)) {
        case IS_LONG: 
            ret.j = val->value.lval;
            break; 
        case IS_DOUBLE:
            ret.d = val->value.dval;
            break; 
        case IS_BOOL: 
            ret.z = (val->value.lval)?true:false;
            break; 
        case IS_ARRAY: 
        case IS_OBJECT: 
        case IS_CONSTANT:
        case IS_STRING: 
            ret.l = zval_to_jobject(env, val);
            break; 
        default:
            ret.l = NULL;
    }

    return ret;
}

/**
 * copies the contents of a zval into a jobject
 */
jobject zval_to_jobject(JNIEnv* env, zval* val) {
    if (NULL == val)
        return NULL;

    TSRMLS_FETCH();

    jclass cls = NULL;
    jobject obj = NULL;
    jmethodID mid = NULL;

    switch (Z_TYPE_P(val)) {
        case IS_LONG: {
            //printf("IS_LONG\n");
            cls = env->FindClass("java/lang/Long");
            mid = env->GetMethodID(cls, "<init>", "(J)V");
            obj = env->NewObject(cls, mid, val->value.lval);
            break; }
        case IS_DOUBLE: {
            //printf("IS_DOUBLE\n");
            cls = env->FindClass("java/lang/Double");
            mid = env->GetMethodID(cls, "<init>", "(D)V");
            obj = env->NewObject(cls, mid, val->value.dval);
            break; }
        case IS_BOOL: {
            //printf("IS_BOOL\n");
            cls = env->FindClass("java/lang/Boolean");
            mid = env->GetMethodID(cls, "<init>", "(Z)V");
            obj = env->NewObject(cls, mid, (val->value.lval)?true:false);
            break; }
        case IS_ARRAY: { 
            //printf("IS_ARRAY\n");
            // get HashTable 
            HashTable* ht = val->value.ht;
            HashPosition pos;
            zval** hashval;

            // allocate HashMap class and object
            cls = env->FindClass("java/util/HashMap");
            mid = env->GetMethodID(cls, "<init>", "()V");
            obj = env->NewObject(cls, mid);
            jmethodID putMID = env->GetMethodID(cls, "put", "(Ljava/lang/Object;Ljava/lang/Object;)Ljava/lang/Object;");

            // reset and iterate on HashTable
            zend_hash_internal_pointer_reset_ex(ht, &pos);
            while (zend_hash_get_current_data_ex(ht, (void **) &hashval, &pos) == SUCCESS) {
                char* key_name;
                ulong num_key;
                uint  str_len;

                //key object
                jobject keyObject = NULL;
                // extract current key
                switch (zend_hash_get_current_key_ex(ht, &key_name, &str_len, &num_key, 0, &pos)) {
                    case HASH_KEY_IS_STRING:
                        keyObject = env->NewStringUTF(key_name);
                    break;
                    case HASH_KEY_IS_LONG:
                        jclass keyClass = env->FindClass("java/lang/Long");
                        jmethodID keyConstructor = env->GetMethodID(keyClass, "<init>", "(J)V");
                        keyObject = env->NewObject(keyClass, keyConstructor, num_key);
                    break;
                }

                // value object
                jobject valObject = zval_to_jobject(env, *hashval);
                // insert pair into hashmap
                env->CallObjectMethod(obj, putMID, keyObject, valObject);

                zend_hash_move_forward_ex(ht, &pos);
            }
            break; }
        case IS_OBJECT: {
            //printf("IS_OBJECT\n");
            // get class entry and object value
            zend_class_entry* ce = Z_OBJCE_P(val);
            zend_object_value* zo = &(val->value.obj);

            // check for special objects
            if (strcmp(ce->name, "TurpitudeJavaObject") == 0) {
                turpitude_javaobject_object* object = (turpitude_javaobject_object*)zend_object_store_get_object(val TSRMLS_CC);
                return object->java_object;
            }
            if (strcmp(ce->name, "TurpitudeJavaClass") == 0) {
                turpitude_javaclass_object* myclass = (turpitude_javaclass_object*)zend_object_store_get_object(val TSRMLS_CC);
                return myclass->java_class;
            }

            // allocate class and object
            jstring phpclassname = env->NewStringUTF(ce->name);
            cls = env->FindClass("net/xp_framework/turpitude/PHPObject");
            mid = env->GetMethodID(cls, "<init>", "(Ljava/lang/String;)V");
            obj = env->NewObject(cls, mid, env->NewStringUTF(ce->name));

            // find methodID of PHPObject.setProperty
            jmethodID setPropID = env->GetMethodID(cls, "setProperty", "(Ljava/lang/String;Ljava/lang/Object;)V");

            // read properties
            HashTable* props = Z_OBJ_HT_P(val)->get_properties(val TSRMLS_CC);
            HashPosition pos;
            zval** prop;

            // reset and iterate on properties HashTable
            zend_hash_internal_pointer_reset_ex(props, &pos);
            while (zend_hash_get_current_data_ex(props, (void **) &prop, &pos) == SUCCESS) {
                // some local variables
                char* prop_name = NULL;
                char* key_val = NULL;
                uint  key_len, prop_name_len;
                ulong index;
                // extract current key, which is the property name
                if (zend_hash_get_current_key_ex(props, &key_val, &key_len, &index, 1, &pos) == HASH_KEY_IS_STRING) {
                    if (key_len) {
                        //printf("key: %s %d\n", key_val, key_len);
                        if (!key_val[0]) {
                            // moronic: private/protected member keys start with \0
                            // we have to "unmangle" them
                            char* class_name;
                            zend_unmangle_property_name(key_val, key_len, &class_name, &prop_name);
                            prop_name_len = strlen(prop_name) + 1;
                        } else {
                            prop_name = key_val;
                            prop_name_len = key_len;
                        }
                        // read member value
                        zval* member;
                        member = zend_read_property(ce, val, prop_name, prop_name_len - 1, 1 TSRMLS_CC);
                        //printf("propname: %s %d\n", prop_name, prop_name_len);
                        if (NULL != member) {
                            // convert member object
                            jobject mo = zval_to_jobject(env, member);
                            // key string
                            jstring keystr = env->NewStringUTF(prop_name);
                            // set property in PHPObject
                            env->CallVoidMethod(obj, setPropID, keystr, mo);
                        }
                    }
                    // housekeeping
                    efree(key_val);
                }
                // move to next HashTable entry
                zend_hash_move_forward_ex(props, &pos);
            }
            break; }
        case IS_CONSTANT:
        case IS_STRING: {
            //printf("IS_STRING or IS_CONSTANT\n");
            obj = env->NewStringUTF(val->value.str.val);
            break; }
        default:
            //printf("default\n");
            // probably null
            obj = NULL;
    }

    return obj;
}

#if ZEND_DEBUG
ZEND_API void _zval_ptr_dtor_wrapper(zval **zval_ptr) {
    zval_ptr_dtor(zval_ptr);
}
#endif

