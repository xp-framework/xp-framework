#include <Turpitude.h>

//####################### global variables ##################################3
zend_object_handlers turpitude_jclass_handlers;
zend_class_entry* turpitude_jclass_class_entry;
zend_object_value turpitude_jclass_object_value;

//####################### method handlers ##################################3
void turpitude_jclass_method_findMethod(turpitude_javaclass_object* cls, int xargc, zval*** xargv, zval* return_value) {
    // check param count
    if (xargc != 2) 
        php_error(E_ERROR, "invalid number of arguments to method findClass.");

    // check param format
    if (Z_TYPE_P(*xargv[0]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 1 (methodname) in method findClass, should be IS_STRING.");

    if (Z_TYPE_P(*xargv[1]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 2 (signature) in method findClass, should be IS_STRING, see JNI documentation for format.");

    make_turpitude_jmethod_instance(cls->java_class, Z_STRVAL_P(*xargv[0]), Z_STRVAL_P(*xargv[1]), return_value, false);
}

void turpitude_jclass_method_findStaticMethod(turpitude_javaclass_object* cls, int xargc, zval*** xargv, zval* return_value) {
    // check param count
    if (xargc != 2) 
        php_error(E_ERROR, "invalid number of arguments to method findStaticMethod.");

    // check param format
    if (Z_TYPE_P(*xargv[0]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 1 (methodname) in method findStaticMethod, should be IS_STRING.");

    if (Z_TYPE_P(*xargv[1]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 2 (signature) in method findStaticMethod, should be IS_STRING, see JNI documentation for format.");

    make_turpitude_jmethod_instance(cls->java_class, Z_STRVAL_P(*xargv[0]), Z_STRVAL_P(*xargv[1]), return_value, true);
}

void turpitude_jclass_method_findConstructor(turpitude_javaclass_object* cls, int xargc, zval*** xargv, zval* return_value) {
    // check param count
    if (xargc != 1) 
        php_error(E_ERROR, "invalid number of arguments to method findConstructor.");

    // check param format
    if (Z_TYPE_P(*xargv[0]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 1 (signature) in method findConstructor, should be IS_STRING, see JNI documentation for format.");

    make_turpitude_jmethod_instance(cls->java_class, "<init>", Z_STRVAL_P(*xargv[0]), return_value);
}

void turpitude_jclass_method_invokestatic(turpitude_javaclass_object* jclass, int xargc, zval*** xargv, zval* return_value) {
     TSRMLS_FETCH();

    // check param count
    if (xargc < 1) 
        php_error(E_ERROR, "invalid number of arguments to method invokeStatic.");

    // check constructor validity
    if (Z_TYPE_P(*xargv[0]) != IS_OBJECT)
        php_error(E_ERROR, "invalid type for param 1 (method) in method invokeStatic, should be IS_OBJECT.");

    zval* methodval = *xargv[0];
    zend_object_value obj = methodval->value.obj;
    zend_class_entry* methodce = Z_OBJCE_P(methodval);

    if (strcmp(methodce->name, "TurpitudeJavaMethod") != 0)
        php_error(E_ERROR, "invalid type for param 1 (method) in method invokeStatic, should be TurpitudeJavaMethod.");

    // get jmethod
    turpitude_javamethod_object* method = (turpitude_javamethod_object*)zend_object_store_get_object(*xargv[0] TSRMLS_CC);

    if (!method->is_static)
        php_error(E_ERROR, "tried to call nonstatic method statically");

    // call java method
    // there might be a better way to do this...
    // it's an awful lot of ifs, isn't it?
    jvalue retval;
    if (xargc > 1) {
        // we need to build the jvalue array
        jvalue args[xargc-1];
        for (int i=0; i < xargc-1; i++) {
            args[i] = zval_to_jvalue(turpitude_jenv, *xargv[i+1]);
        }
        switch (method->return_type) {
            case JAVA_VOID: 
                turpitude_jenv->CallStaticVoidMethodA(jclass->java_class, method->java_method, args); 
                break;
            case JAVA_OBJECT:
                retval.l = turpitude_jenv->CallStaticObjectMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_BOOLEAN:
                retval.z = turpitude_jenv->CallStaticBooleanMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_BYTE:
                retval.b = turpitude_jenv->CallStaticByteMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_CHAR:
                retval.c = turpitude_jenv->CallStaticCharMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_SHORT:
                retval.s = turpitude_jenv->CallStaticShortMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_INT:
                retval.i = turpitude_jenv->CallStaticIntMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_LONG:
                retval.j = turpitude_jenv->CallStaticLongMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_FLOAT:
                retval.f = turpitude_jenv->CallStaticFloatMethodA(jclass->java_class, method->java_method, args);
                break;
            case JAVA_DOUBLE:
                retval.d = turpitude_jenv->CallStaticDoubleMethodA(jclass->java_class, method->java_method, args);
                break;
        };
    } else {
        // no parameters given - just call the method
        switch (method->return_type) {
            case JAVA_VOID: 
                turpitude_jenv->CallStaticVoidMethod(jclass->java_class, method->java_method); 
                break;
            case JAVA_OBJECT:
                retval.l = turpitude_jenv->CallStaticObjectMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_BOOLEAN:
                retval.z = turpitude_jenv->CallStaticBooleanMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_BYTE:
                retval.b = turpitude_jenv->CallStaticByteMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_CHAR:
                retval.c = turpitude_jenv->CallStaticCharMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_SHORT:
                retval.s = turpitude_jenv->CallStaticShortMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_INT:
                retval.i = turpitude_jenv->CallStaticIntMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_LONG:
                retval.j = turpitude_jenv->CallStaticLongMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_FLOAT:
                retval.f = turpitude_jenv->CallStaticFloatMethod(jclass->java_class, method->java_method);
                break;
            case JAVA_DOUBLE:
                retval.d = turpitude_jenv->CallStaticDoubleMethod(jclass->java_class, method->java_method);
                break;
        };
    }

    jvalue_to_zval(turpitude_jenv, retval, method->return_type, return_value);
}

void turpitude_jclass_method_create(turpitude_javaclass_object* cls, zval* turpcls, int xargc, zval*** xargv, zval* return_value) {
    TSRMLS_FETCH();

    // check param count
    if (xargc < 1) 
        php_error(E_ERROR, "invalid number of arguments to method create.");

    // check constructor validity
    if (Z_TYPE_P(*xargv[0]) != IS_OBJECT)
        php_error(E_ERROR, "invalid type for param 1 (constructor) in method create, should be IS_OBJECT.");

    zval* constrval = *xargv[0];
    zend_object_value obj = constrval->value.obj;
    zend_class_entry* constrce = Z_OBJCE_P(constrval);

    if (strcmp(constrce->name, "TurpitudeJavaMethod") != 0)
        php_error(E_ERROR, "invalid type for param 1 (constructor) in method create, should be TurpitudeJavaMethod.");

    // get jmethodID
    turpitude_javamethod_object* method = (turpitude_javamethod_object*)zend_object_store_get_object(*xargv[0] TSRMLS_CC);


    // create java object
    jobject jobj;
    if (xargc > 1) {
        // build jvalue array
        jvalue args[xargc-1];
        for (int i=0; i < xargc-1; i++) {
            args[i] = zval_to_jvalue(turpitude_jenv, *xargv[i+1]);
        }
        jobj = turpitude_jenv->NewObjectA(cls->java_class, method->java_method, args);
    } else {
        jobj = turpitude_jenv->NewObject(cls->java_class, method->java_method);
    }

    make_turpitude_jobject_instance(cls->java_class, turpcls, jobj, return_value);

}

//####################### helpers ##################################3

static
    ZEND_BEGIN_ARG_INFO(turpitude_jclass_arginfo_zero, 0)
    ZEND_END_ARG_INFO();

static
    ZEND_BEGIN_ARG_INFO(turpitude_jclass_arginfo_get, 0)
    ZEND_ARG_INFO(0, index)
    ZEND_END_ARG_INFO();


static ZEND_BEGIN_ARG_INFO(turpitude_jclass_arginfo_set, 0)
     ZEND_ARG_INFO(0, index)
     ZEND_ARG_INFO(0, newval)
     ZEND_END_ARG_INFO();

//####################### object handlers ##################################3

void turpitude_jclass_construct(INTERNAL_FUNCTION_PARAMETERS) {
}

void turpitude_jclass_call(INTERNAL_FUNCTION_PARAMETERS) {
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

    // method parameters
    xargc = zend_hash_num_elements(Z_ARRVAL_PP(argv[1]));
    xargv = (zval***) safe_emalloc(sizeof(zval **), xargc, 0);
    // iterate on argument HashTable
    zend_hash_internal_pointer_reset_ex(Z_ARRVAL_PP(argv[1]), &pos);
    while (zend_hash_get_current_data_ex(Z_ARRVAL_PP(argv[1]), (void **) &param, &pos) == SUCCESS) {
        xargv[i++] = param; 
        zend_hash_move_forward_ex(Z_ARRVAL_PP(argv[1]), &pos);
    }

    // this and class pointer 
    zval* myval = getThis();
    turpitude_javaclass_object* cls = (turpitude_javaclass_object*)zend_object_store_get_object(myval TSRMLS_CC);

    bool method_valid = false;
    
    if (strcmp(Z_STRVAL_P(*argv[0]), "findMethod") == 0) {
        turpitude_jclass_method_findMethod(cls, xargc, xargv, return_value);
        method_valid = true;
    }

    if (strcmp(Z_STRVAL_P(*argv[0]), "findStaticMethod") == 0) {
        turpitude_jclass_method_findStaticMethod(cls, xargc, xargv, return_value);
        method_valid = true;
    }

    if (strcmp(Z_STRVAL_P(*argv[0]), "findConstructor") == 0) {
        turpitude_jclass_method_findConstructor(cls, xargc, xargv, return_value);
        method_valid = true;
    }

    if (strcmp(Z_STRVAL_P(*argv[0]), "invokeStatic") == 0) {
        turpitude_jclass_method_invokestatic(cls, xargc, xargv, return_value);
        method_valid = true;
    }

    if (strcmp(Z_STRVAL_P(*argv[0]), "create") == 0) {
        turpitude_jclass_method_create(cls, myval, xargc, xargv, return_value);
        method_valid = true;
    }

    char* errmsg = (char*)emalloc(100 + strlen(method_name));
    memset(errmsg, 0, 99 + strlen(method_name));
    if (!method_valid) { 
        sprintf(errmsg, "Call to invalid method %s() on object of class TurpitudeJavaClass.", method_name);
        php_error(E_ERROR, errmsg);
    }

    // housekeeping
    efree(errmsg);
    efree(argv);
    efree(xargv);
}

void turpitude_jclass_tostring(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__tostring called\n");
}

void turpitude_jclass_get(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__get called\n");
    //php_error(E_ERROR, "Tried to directly get a property on object of class TurpitudeEnvironment.");
}

void turpitude_jclass_set(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__set called\n");
    //php_error(E_ERROR, "Tried to directly set a property on object of class TurpitudeEnvironment.");
}

void turpitude_jclass_sleep(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__sleep called\n");
}

void turpitude_jclass_wakeup(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__wakeup called\n");
}

void turpitude_jclass_destruct(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__destruct called\n");
}

int turpitude_jclass_cast(zval *readobj, zval *writeobj, int type TSRMLS_DC) {
    printf("__cast called\n");
    return FAILURE;
}

zend_object_iterator* turpitude_jclass_get_iterator(zend_class_entry *ce, zval *object, int by_ref TSRMLS_DC) {
    printf("get_iterator called\n");
    return NULL;
}

void turpitude_jclass_free_object(void *object TSRMLS_DC) {
    turpitude_javaclass_object* intern = (turpitude_javaclass_object*)object;
    zend_hash_destroy(intern->std.properties);
    FREE_HASHTABLE(intern->std.properties);
    efree(object);
}

void turpitude_jclass_destroy_object(void* object, zend_object_handle handle TSRMLS_DC) {
    //printf("destroy object called\n");
}

zend_object_value turpitude_jclass_create_object(zend_class_entry *class_type TSRMLS_DC) {
    zend_object_value obj;
    turpitude_javaclass_object* intern;
    zval tmp;

    intern = (turpitude_javaclass_object*)emalloc(sizeof(turpitude_javaclass_object));
    memset(intern, 0, sizeof(turpitude_javaclass_object));
    intern->std.ce = class_type;

    ALLOC_HASHTABLE(intern->std.properties);
    zend_hash_init(intern->std.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
    zend_hash_copy(intern->std.properties,
                   &class_type->default_properties,
                   (copy_ctor_func_t) zval_add_ref,
                   (void *) &tmp, sizeof(zval *));
    obj.handle = zend_objects_store_put(intern,  
                                        (zend_objects_store_dtor_t) turpitude_jclass_destroy_object,
                                        (zend_objects_free_object_storage_t)turpitude_jclass_free_object,
                                        NULL TSRMLS_CC);
    obj.handlers = &turpitude_jclass_handlers;

    return obj;
}

function_entry turpitude_jclass_class_functions[] = {
    ZEND_FENTRY(__construct, turpitude_jclass_construct, NULL, ZEND_ACC_PRIVATE) 
    ZEND_FENTRY(__call, turpitude_jclass_call, turpitude_jclass_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__tostring, turpitude_jclass_tostring, turpitude_jclass_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__get, turpitude_jclass_get, turpitude_jclass_arginfo_get, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__set, turpitude_jclass_set, turpitude_jclass_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__sleep, turpitude_jclass_sleep, turpitude_jclass_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__wakeup, turpitude_jclass_wakeup, turpitude_jclass_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__destruct, turpitude_jclass_destruct, turpitude_jclass_arginfo_zero, ZEND_ACC_PUBLIC)
    {NULL, NULL, NULL}
};

//####################### API ##################################3

/**
 * creates the Turpitude JavaClass and injects it into the interpreter
 */
void make_turpitude_jclass() {
    TSRMLS_FETCH();

    // create class entry
    zend_class_entry* parent;
    zend_class_entry ce;

    zend_internal_function call, get, set;
    make_lambda(&call, turpitude_jclass_call);
    make_lambda(&get, turpitude_jclass_get);
    make_lambda(&set, turpitude_jclass_set);

    INIT_OVERLOADED_CLASS_ENTRY(ce, 
                                "TurpitudeJavaClass", 
                                turpitude_jclass_class_functions, 
                                (zend_function*)&call, 
                                (zend_function*)&get, 
                                (zend_function*)&set);

    memcpy(&turpitude_jclass_handlers, zend_get_std_object_handlers(), sizeof(turpitude_jclass_handlers));
    turpitude_jclass_handlers.cast_object = turpitude_jclass_cast;
    
    turpitude_jclass_class_entry = zend_register_internal_class(&ce TSRMLS_CC);
    turpitude_jclass_class_entry->get_iterator = turpitude_jclass_get_iterator;
    turpitude_jclass_class_entry->create_object = turpitude_jclass_create_object;
}

void make_turpitude_jclass_instance(char* classname, zval* dest) {
    TSRMLS_FETCH();

    if (!dest)
        ALLOC_ZVAL(dest);
    
    // use JNIEnv to find the desired java class
    jclass cls = turpitude_jenv->FindClass(classname);
    if (cls == NULL) {
        php_error(E_ERROR, "unable to find java class %s", classname);
    }

    make_turpitude_jclass_instance(cls, classname, dest);
}

void make_turpitude_jclass_instance(jclass cls, char* classname, zval* dest) {
    if (!dest)
        ALLOC_ZVAL(dest);
    // instantiate JavaClass object
    Z_TYPE_P(dest) = IS_OBJECT;
    object_init_ex(dest, turpitude_jclass_class_entry);
    dest->refcount = 1;
    dest->is_ref = 1;

    // assign jclass to object
    turpitude_javaclass_object* intern = (turpitude_javaclass_object*)zend_object_store_get_object(dest TSRMLS_CC);
    intern->java_class = cls;

    // copy classname to name and add it as a property
    zval* name;
    MAKE_STD_ZVAL(name);
    ZVAL_STRING(name, classname, 1);
    zend_hash_update(Z_OBJPROP_P(dest), "ClassName", sizeof("ClassName"), (void **) &name, sizeof(zval *), NULL);
}

