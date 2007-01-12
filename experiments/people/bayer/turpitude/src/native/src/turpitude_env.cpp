#include <Turpitude.h>

//####################### global variables ##################################3
zend_object_handlers turpitude_env_handlers;
zend_class_entry* turpitude_env_class_entry;
zend_object_value turpitude_env_object_value;


typedef struct tupitude_environment_object {
    zend_object     std;
    jobject         script_context;
    JNIEnv*         java_env;
};

//####################### method handlers ##################################3

void turpitude_env_method_findClass(int xargc, zval*** xargv, zval* return_value) {
    // check param count
    if (xargc != 1) 
        php_error(E_ERROR, "invalid number of arguments to method findClass.");

    if (Z_TYPE_P(*xargv[0]) != IS_STRING) 
        php_error(E_ERROR, "invalid type for param 1 in method findClass, should be IS_STRING, see JNI documentation for format.");
    
    make_turpitude_jclass_instance(Z_STRVAL_P(*xargv[0]), return_value);
}

//####################### parameter pointers ##################################3

static
    ZEND_BEGIN_ARG_INFO(turpitude_env_arginfo_zero, 0)
    ZEND_END_ARG_INFO();

static
    ZEND_BEGIN_ARG_INFO(turpitude_env_arginfo_get, 0)
    ZEND_ARG_INFO(0, index)
    ZEND_END_ARG_INFO();


static ZEND_BEGIN_ARG_INFO(turpitude_env_arginfo_set, 0)
     ZEND_ARG_INFO(0, index)
     ZEND_ARG_INFO(0, newval)
     ZEND_END_ARG_INFO();

//####################### object handlers ##################################3

void turpitude_env_construct(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__construct called\n");
}

void turpitude_env_call(INTERNAL_FUNCTION_PARAMETERS) {
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

    bool method_valid = false;

    if (strcmp(Z_STRVAL_P(*argv[0]), "findClass") == 0) {
        turpitude_env_method_findClass(xargc, xargv, return_value);
        method_valid = true;
    }

    char* errmsg = (char*)emalloc(100 + strlen(method_name));
    memset(errmsg, 0, 99 + strlen(method_name));
    if (!method_valid) { 
        sprintf(errmsg, "Call to invalid method %s() on object of class TurpitudeEnvironment.", method_name);
        php_error(E_ERROR, errmsg);
    }

    // housekeeping
    efree(errmsg);
    efree(argv);
    efree(xargv);
}

void turpitude_env_tostring(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__tostring called\n");
}

void turpitude_env_get(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__get called\n");
    //php_error(E_ERROR, "Tried to directly get a property on object of class TurpitudeEnvironment.");
}

void turpitude_env_set(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__set called\n");
    //php_error(E_ERROR, "Tried to directly set a property on object of class TurpitudeEnvironment.");
}

void turpitude_env_sleep(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__sleep called\n");
}

void turpitude_env_wakeup(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__wakeup called\n");
}

void turpitude_env_destruct(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__destruct called\n");
}

int turpitude_env_cast(zval *readobj, zval *writeobj, int type TSRMLS_DC) {
    //printf("__cast called\n");
    return FAILURE;
}

zend_object_iterator* turpitude_env_get_iterator(zend_class_entry *ce, zval *object, int by_ref TSRMLS_DC) {
    //printf("get_iterator called\n");
    return NULL;
}

void turpitude_env_free_object(void *object TSRMLS_DC) {
    tupitude_environment_object* intern = (tupitude_environment_object*)object;
    zend_hash_destroy(intern->std.properties);
    FREE_HASHTABLE(intern->std.properties);
    efree(object);
}

void turpitude_env_destroy_object(void* object, zend_object_handle handle TSRMLS_DC) {
    //printf("destroy object called\n");
}

zend_object_value turpitude_env_create_object(zend_class_entry *class_type TSRMLS_DC) {
    zend_object_value obj;
    tupitude_environment_object* intern;
    zval tmp;

    intern = (tupitude_environment_object*)emalloc(sizeof(tupitude_environment_object));
    memset(intern, 0, sizeof(tupitude_environment_object));
    intern->std.ce = class_type;

    ALLOC_HASHTABLE(intern->std.properties);
    zend_hash_init(intern->std.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
    zend_hash_copy(intern->std.properties,
                   &class_type->default_properties,
                   (copy_ctor_func_t) zval_add_ref,
                   (void *) &tmp, sizeof(zval *));
    obj.handle = zend_objects_store_put(intern,  
                                        (zend_objects_store_dtor_t) turpitude_env_destroy_object,
                                        (zend_objects_free_object_storage_t)turpitude_env_free_object,
                                        NULL TSRMLS_CC);
    obj.handlers = &turpitude_env_handlers;

    return obj;
}

function_entry turpitude_env_class_functions[] = {
    ZEND_FENTRY(__construct, turpitude_env_construct, NULL, 0) 
    ZEND_FENTRY(__call, turpitude_env_call, turpitude_env_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__tostring, turpitude_env_tostring, turpitude_env_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__get, turpitude_env_get, turpitude_env_arginfo_get, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__set, turpitude_env_set, turpitude_env_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__sleep, turpitude_env_sleep, turpitude_env_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__wakeup, turpitude_env_wakeup, turpitude_env_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__destruct, turpitude_env_destruct, turpitude_env_arginfo_zero, ZEND_ACC_PUBLIC)
    {NULL, NULL, NULL}
};

//####################### API ##################################3

/**
 * creates the Turpitude Class and injects it into the interpreter
 */
void make_turpitude_environment(JNIEnv* env, jobject ctx) {
    turpitude_jenv = env;
    turpitude_current_script_context = ctx;

    // create class entry
    zend_class_entry* parent;
    zend_class_entry ce;

    zend_internal_function call, get, set;
    make_lambda(&call, turpitude_env_call);
    make_lambda(&get, turpitude_env_get);
    make_lambda(&set, turpitude_env_set);

    INIT_OVERLOADED_CLASS_ENTRY(ce, 
                                "TurpitudeEnvironment", 
                                turpitude_env_class_functions, 
                                (zend_function*)&call, 
                                (zend_function*)&get, 
                                (zend_function*)&set);

    memcpy(&turpitude_env_handlers, zend_get_std_object_handlers(), sizeof(turpitude_env_handlers));
    turpitude_env_handlers.cast_object = turpitude_env_cast;
    
    turpitude_env_class_entry = zend_register_internal_class(&ce TSRMLS_CC);
    turpitude_env_class_entry->get_iterator = turpitude_env_get_iterator;
    turpitude_env_class_entry->create_object = turpitude_env_create_object;
}


