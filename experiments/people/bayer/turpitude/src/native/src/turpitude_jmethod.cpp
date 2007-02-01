#include <Turpitude.h>

//####################### global variables ##################################3
zend_object_handlers turpitude_jmethod_handlers;
zend_class_entry* turpitude_jmethod_class_entry;
zend_object_value turpitude_jmethod_object_value;


//####################### method handlers ##################################3

//####################### helpers ##################################3

static
    ZEND_BEGIN_ARG_INFO(turpitude_jmethod_arginfo_zero, 0)
    ZEND_END_ARG_INFO();

static
    ZEND_BEGIN_ARG_INFO(turpitude_jmethod_arginfo_get, 0)
    ZEND_ARG_INFO(0, index)
    ZEND_END_ARG_INFO();


static ZEND_BEGIN_ARG_INFO(turpitude_jmethod_arginfo_set, 0)
     ZEND_ARG_INFO(0, index)
     ZEND_ARG_INFO(0, newval)
     ZEND_END_ARG_INFO();

//####################### object handlers ##################################3

void turpitude_jmethod_construct(INTERNAL_FUNCTION_PARAMETERS) {
}

void turpitude_jmethod_call(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__call called:\n");
    
    zval ***xargv, ***argv;
    int i = 0, xargc, argc = ZEND_NUM_ARGS();
    HashPosition pos;
    zval **param;

    // method name
    argv = (zval ***) safe_emalloc(sizeof(zval **), argc, 0);
    if (zend_get_parameters_array_ex(argc, argv) == FAILURE) {
        php_error(E_ERROR, "Couldn't fetch arguments into array.");
    }
    char* method_name = Z_STRVAL_P(*argv[0]);
    printf("method: %s, %d params\n", method_name, argc);

    // method parameters
    xargc = zend_hash_num_elements(Z_ARRVAL_PP(argv[1]));
    xargv = (zval***) safe_emalloc(sizeof(zval **), xargc, 0);
    // iterate on argument HashTable
    zend_hash_internal_pointer_reset_ex(Z_ARRVAL_PP(argv[1]), &pos);
    while (zend_hash_get_current_data_ex(Z_ARRVAL_PP(argv[1]), (void **) &param, &pos) == SUCCESS) {
        xargv[i++] = param; 
        zend_hash_move_forward_ex(Z_ARRVAL_PP(argv[1]), &pos);
    }

    bool method_valid = false;
    
    /*
    if (strcmp(Z_STRVAL_P(*argv[0]), "findClass") == 0) {
        turpitude_jmethod_method_findClass(xargc, xargv);
        method_valid = true;
    }
    */

    char* errmsg = (char*)emalloc(100 + strlen(method_name));
    memset(errmsg, 0, 99 + strlen(method_name));
    if (!method_valid) { 
        sprintf(errmsg, "Call to invalid method %s() on object of class TurpitudeJavaMethod.", method_name);
        php_error(E_ERROR, errmsg);
    }

    // housekeeping
    efree(errmsg);
    efree(argv);
    efree(xargv);
}

void turpitude_jmethod_tostring(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__tostring called\n");
}

void turpitude_jmethod_get(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__get called\n");
    //php_error(E_ERROR, "Tried to directly get a property on object of class TurpitudeEnvironment.");
}

void turpitude_jmethod_set(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__set called\n");
    //php_error(E_ERROR, "Tried to directly set a property on object of class TurpitudeEnvironment.");
}

void turpitude_jmethod_sleep(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__sleep called\n");
}

void turpitude_jmethod_wakeup(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__wakeup called\n");
}

void turpitude_jmethod_destruct(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__destruct called\n");
}

int turpitude_jmethod_cast(zval *readobj, zval *writeobj, int type TSRMLS_DC) {
    printf("__cast called\n");
    return FAILURE;
}

zend_object_iterator* turpitude_jmethod_get_iterator(zend_class_entry *ce, zval *object, int by_ref TSRMLS_DC) {
    printf("get_iterator called\n");
    return NULL;
}

void turpitude_jmethod_free_object(void *object TSRMLS_DC) {
    turpitude_javamethod_object* intern = (turpitude_javamethod_object*)object;
    zend_hash_destroy(intern->std.properties);
    FREE_HASHTABLE(intern->std.properties);
    efree(object);
}

void turpitude_jmethod_destroy_object(void* object, zend_object_handle handle TSRMLS_DC) {
    //printf("destroy object called\n");
}

zend_object_value turpitude_jmethod_create_object(zend_class_entry *class_type TSRMLS_DC) {
    zend_object_value obj;
    turpitude_javamethod_object* intern;
    zval tmp;

    intern = (turpitude_javamethod_object*)emalloc(sizeof(turpitude_javamethod_object));
    memset(intern, 0, sizeof(turpitude_javamethod_object));
    intern->std.ce = class_type;

    ALLOC_HASHTABLE(intern->std.properties);
    zend_hash_init(intern->std.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
    zend_hash_copy(intern->std.properties,
                   &class_type->default_properties,
                   (copy_ctor_func_t) zval_add_ref,
                   (void *) &tmp, sizeof(zval *));
    obj.handle = zend_objects_store_put(intern,  
                                        (zend_objects_store_dtor_t) turpitude_jmethod_destroy_object,
                                        (zend_objects_free_object_storage_t)turpitude_jmethod_free_object,
                                        NULL TSRMLS_CC);
    obj.handlers = &turpitude_jmethod_handlers;

    return obj;
}

function_entry turpitude_jmethod_class_functions[] = {
    ZEND_FENTRY(__construct, turpitude_jmethod_construct, NULL, ZEND_ACC_PRIVATE) 
    ZEND_FENTRY(__call, turpitude_jmethod_call, turpitude_jmethod_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__tostring, turpitude_jmethod_tostring, turpitude_jmethod_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__get, turpitude_jmethod_get, turpitude_jmethod_arginfo_get, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__set, turpitude_jmethod_set, turpitude_jmethod_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__sleep, turpitude_jmethod_sleep, turpitude_jmethod_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__wakeup, turpitude_jmethod_wakeup, turpitude_jmethod_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__destruct, turpitude_jmethod_destruct, turpitude_jmethod_arginfo_zero, ZEND_ACC_PUBLIC)
    {NULL, NULL, NULL}
};

//####################### API ##################################3

/**
 * creates the TurpitudeJavaMethod class and injects it into the interpreter
 */
void make_turpitude_jmethod() {
    // create class entry
    zend_class_entry* parent;
    zend_class_entry ce;

    zend_internal_function call, get, set;
    make_lambda(&call, turpitude_jmethod_call);
    make_lambda(&get, turpitude_jmethod_get);
    make_lambda(&set, turpitude_jmethod_set);

    INIT_OVERLOADED_CLASS_ENTRY(ce, 
                                "TurpitudeJavaMethod", 
                                turpitude_jmethod_class_functions, 
                                (zend_function*)&call, 
                                (zend_function*)&get, 
                                (zend_function*)&set);

    memcpy(&turpitude_jmethod_handlers, zend_get_std_object_handlers(), sizeof(turpitude_jmethod_handlers));
    turpitude_jmethod_handlers.cast_object = turpitude_jmethod_cast;
    
    turpitude_jmethod_class_entry = zend_register_internal_class(&ce TSRMLS_CC);
    turpitude_jmethod_class_entry->get_iterator = turpitude_jmethod_get_iterator;
    turpitude_jmethod_class_entry->create_object = turpitude_jmethod_create_object;
}

void make_turpitude_jmethod_instance(jclass cls, char* name, char* sig, zval* dest, bool is_static) {
    if (!dest)
        ALLOC_ZVAL(dest);
  
    // use JNIEnv to find the desired java class
    jmethodID mid;
    if (is_static) 
        mid = turpitude_jenv->GetStaticMethodID(cls, name, sig);
    else 
        mid = turpitude_jenv->GetMethodID(cls, name, sig);

    if (mid == NULL) {
        php_error(E_ERROR, "unable to find java method %s with signature %s", name, sig);
    }

    // instantiate JavaClass object
    Z_TYPE_P(dest) = IS_OBJECT;
    object_init_ex(dest, turpitude_jmethod_class_entry);
    dest->refcount = 1;
    dest->is_ref = 1;

    // assign jclass and methodid to object
    turpitude_javamethod_object* intern = (turpitude_javamethod_object*)zend_object_store_get_object(dest TSRMLS_CC);
    intern->java_class = cls;
    intern->java_method = mid;

    // find return type
    char* sp = sig;
    char c;
    // move string pointer to start of return type
    while (c = *sp) {
        if (c == ')') {
            c = *++sp;
            break;
        }
        sp++;
    }
    turpitude_java_type rt = get_java_field_type(sp);
    /*
    switch (c) {
        case 'Z': rt = JAVA_BOOLEAN;  break;  
        case 'B': rt = JAVA_BYTE;     break;  
        case 'C': rt = JAVA_CHAR;     break;  
        case 'S': rt = JAVA_SHORT;    break;  
        case 'I': rt = JAVA_INT;      break;  
        case 'J': rt = JAVA_LONG;     break;  
        case 'F': rt = JAVA_FLOAT;    break;  
        case 'D': rt = JAVA_DOUBLE;   break;  
        case 'V': rt = JAVA_VOID;     break;
        case 'L': rt = JAVA_OBJECT;   break;
        default:
        // none of the above - throw an error
        php_error(E_ERROR, "unable to determine method return type.");
    }
    */
    intern->return_type = rt;

    intern->is_static = is_static;

    // copy method name and signature to name and add it as a property
    zval* methodname;
    MAKE_STD_ZVAL(methodname);
    ZVAL_STRING(methodname, name, 1);
    zend_hash_update(Z_OBJPROP_P(dest), "MethodName", sizeof("MethodName"), (void **) &methodname, sizeof(zval *), NULL);
    zval* signature;
    MAKE_STD_ZVAL(signature);
    ZVAL_STRING(signature, sig, 1);
    zend_hash_update(Z_OBJPROP_P(dest), "Signature", sizeof("Signature"), (void **) &signature, sizeof(zval *), NULL);
    zval* isstatic;
    MAKE_STD_ZVAL(isstatic);
    ZVAL_BOOL(isstatic, isstatic);
    zend_hash_update(Z_OBJPROP_P(dest), "isStatic", sizeof("isStatic"), (void **) &isstatic, sizeof(zval *), NULL);
}

