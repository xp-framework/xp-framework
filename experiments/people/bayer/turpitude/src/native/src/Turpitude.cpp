#include <Turpitude.h>

zval* generateTurpitudeContext(JNIEnv* env, jobject ctx) {
    zval* context;
    MAKE_STD_ZVAL(context);
    ZVAL_LONG(context, 10);

    // create class entry
    zend_class_entry* ce;
    ce = (zend_class_entry*)emalloc(sizeof(zend_class_entry));
    ce->type = ZEND_USER_CLASS;
    ce->name = "TurpitudeContext";
    ce->name_length = strlen(ce->name);
    ce->parent = NULL;
    ce->refcount = 1;
    ce->constants_updated = 1;
    ce->ce_flags = 0;
    ce->interfaces = NULL;
    ce->num_interfaces = 0;
    ce->filename = "turpitude generated";
    ce->line_start = 1;
    ce->line_end = 1;
    ce->doc_comment = NULL;
    ce->doc_comment_len = 0;


    //zend_object_std_init(ce);
    // how to create a class entry?

    return context;
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
                //efree(key_name);

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

            // ############ remove
            printf("=====> %s\n", ce->doc_comment);
            // ############ remove

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

