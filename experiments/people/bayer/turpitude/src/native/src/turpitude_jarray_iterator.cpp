#include <Turpitude.h>

//####################### global variables ##################################3
zend_object_handlers turpitude_jarray_iterator_handlers;
zend_class_entry* turpitude_jarray_iterator_class_entry;
zend_object_value turpitude_javaarray_iterator_object_value;

//####################### method handlers ##################################3

//####################### helpers ##################################3

static
    ZEND_BEGIN_ARG_INFO(turpitude_jarray_iterator_arginfo_zero, 0)
    ZEND_END_ARG_INFO();

static
    ZEND_BEGIN_ARG_INFO(turpitude_jarray_iterator_arginfo_get, 0)
    ZEND_ARG_INFO(0, index)
    ZEND_END_ARG_INFO();


static ZEND_BEGIN_ARG_INFO(turpitude_jarray_iterator_arginfo_set, 0)
     ZEND_ARG_INFO(0, index)
     ZEND_ARG_INFO(0, newval)
     ZEND_END_ARG_INFO();

//####################### object handlers ##################################3

void turpitude_jarray_iterator_construct(INTERNAL_FUNCTION_PARAMETERS) {
}

void turpitude_jarray_iterator_call(INTERNAL_FUNCTION_PARAMETERS) {
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

    // extract jarray from this pointer
    zval* myval = getThis();
    turpitude_javaarray_iterator_object* jarr = (turpitude_javaarray_iterator_object*)zend_object_store_get_object(myval TSRMLS_CC);

    bool method_valid = false;

    // error handling
    char* errmsg = (char*)emalloc(100 + strlen(method_name));
    memset(errmsg, 0, 99 + strlen(method_name));
    if (!method_valid) { 
        sprintf(errmsg, "Call to invalid method %s() on object of class TurpitudeJavaObject.", method_name);
        php_error(E_ERROR, errmsg);
    }

    // housekeeping
    efree(errmsg);
    efree(argv);
    efree(xargv);
}

void turpitude_jarray_iterator_tostring(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__tostring called\n");
}

void turpitude_jarray_iterator_get(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__get called\n");
    //php_error(E_ERROR, "Tried to directly get a property on object of class TurpitudeEnvironment.");
}

void turpitude_jarray_iterator_set(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__set called\n");
    //php_error(E_ERROR, "Tried to directly set a property on object of class TurpitudeEnvironment.");
}

void turpitude_jarray_iterator_sleep(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__sleep called\n");
}

void turpitude_jarray_iterator_wakeup(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__wakeup called\n");
}

void turpitude_jarray_iterator_destruct(INTERNAL_FUNCTION_PARAMETERS) {
    //printf("__destruct called\n");
}

int turpitude_jarray_iterator_cast(zval *readobj, zval *writeobj, int type TSRMLS_DC) {
    //printf("__cast called\n");
    return FAILURE;
}

zend_object_iterator* turpitude_jarray_iterator_get_iterator(zend_class_entry *ce, zval *object, int by_ref TSRMLS_DC) {
    //printf("get_iterator called\n");
    return NULL;
}

void turpitude_jarray_iterator_free_object(void *object TSRMLS_DC) {
    turpitude_javaobject_object* intern = (turpitude_javaobject_object*)object;
    zend_hash_destroy(intern->std.properties);
    FREE_HASHTABLE(intern->std.properties);
    efree(object);
}

void turpitude_jarray_iterator_destroy_object(void* object, zend_object_handle handle TSRMLS_DC) {
    //printf("destroy object called\n");
}

zend_object_value turpitude_jarray_iterator_create_object(zend_class_entry *class_type TSRMLS_DC) {
    zend_object_value obj;
    turpitude_javaobject_object* intern;
    zval tmp;

    intern = (turpitude_javaobject_object*)emalloc(sizeof(turpitude_javaobject_object));
    memset(intern, 0, sizeof(turpitude_javaobject_object));
    intern->std.ce = class_type;

    ALLOC_HASHTABLE(intern->std.properties);
    zend_hash_init(intern->std.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
    zend_hash_copy(intern->std.properties,
                   &class_type->default_properties,
                   (copy_ctor_func_t) zval_add_ref,
                   (void *) &tmp, sizeof(zval *));
    obj.handle = zend_objects_store_put(intern,  
                                        (zend_objects_store_dtor_t) turpitude_jarray_iterator_destroy_object,
                                        (zend_objects_free_object_storage_t)turpitude_jarray_iterator_free_object,
                                        NULL TSRMLS_CC);
    obj.handlers = &turpitude_jarray_iterator_handlers;

    return obj;
}

function_entry turpitude_jarray_iterator_class_functions[] = {
    ZEND_FENTRY(__construct, turpitude_jarray_iterator_construct, NULL, ZEND_ACC_PRIVATE) 
    ZEND_FENTRY(__call, turpitude_jarray_iterator_call, turpitude_jarray_iterator_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__tostring, turpitude_jarray_iterator_tostring, turpitude_jarray_iterator_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__get, turpitude_jarray_iterator_get, turpitude_jarray_iterator_arginfo_get, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__set, turpitude_jarray_iterator_set, turpitude_jarray_iterator_arginfo_set, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__sleep, turpitude_jarray_iterator_sleep, turpitude_jarray_iterator_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__wakeup, turpitude_jarray_iterator_wakeup, turpitude_jarray_iterator_arginfo_zero, ZEND_ACC_PUBLIC)
    ZEND_FENTRY(__destruct, turpitude_jarray_iterator_destruct, turpitude_jarray_iterator_arginfo_zero, ZEND_ACC_PUBLIC)
    //ZEND_FENTRY(offsetUnset, turpitude_jarray_iterator_offsetUnset, turpitude_jarray_iterator_arginfo_get, ZEND_ACC_PUBLIC)
    {NULL, NULL, NULL}
};

//####################### API ##################################3

/**
 * creates the TurpitudeJavaArray class and injects it into the interpreter
 */
void make_turpitude_jarray_iterator() {
    // create class entry
    zend_class_entry* parent;
    zend_class_entry ce;

    zend_internal_function call, get, set;
    make_lambda(&call, turpitude_jarray_iterator_call);
    make_lambda(&get, turpitude_jarray_iterator_get);
    make_lambda(&set, turpitude_jarray_iterator_set);

    //zend_hash_init_ex(&(ce.function_table), 0, NULL, ZEND_FUNCTION_DTOR, 1, 0);
    INIT_OVERLOADED_CLASS_ENTRY(ce, 
                                "TurpitudeJavaArrayIterator", 
                                turpitude_jarray_iterator_class_functions, 
                                (zend_function*)&call, 
                                (zend_function*)&get, 
                                (zend_function*)&set);

    memcpy(&turpitude_jarray_iterator_handlers, zend_get_std_object_handlers(), sizeof(turpitude_jarray_iterator_handlers));
    turpitude_jarray_iterator_handlers.cast_object = turpitude_jarray_iterator_cast;
  
    turpitude_jarray_iterator_class_entry = zend_register_internal_class(&ce TSRMLS_CC);
    turpitude_jarray_iterator_class_entry->get_iterator = turpitude_jarray_iterator_get_iterator;
    turpitude_jarray_iterator_class_entry->create_object = turpitude_jarray_iterator_create_object;

    // arrays can be accessed as arrays and can be iterated on
    //zend_class_implements(turpitude_jarray_iterator_class_entry TSRMLS_CC, 2, zend_ce_arrayaccess, zend_ce_aggregate);
    zend_class_implements(turpitude_jarray_iterator_class_entry TSRMLS_CC, 1, zend_ce_arrayaccess, zend_ce_aggregate);
}

void make_turpitude_jarray_iterator_instance(jarray array, zval* dest) {
    if (!dest)
        ALLOC_ZVAL(dest);

    // instantiate JavaObject object
    Z_TYPE_P(dest) = IS_OBJECT;
    object_init_ex(dest, turpitude_jarray_iterator_class_entry);
    dest->refcount = 1;
    dest->is_ref = 1;

    // length
    int array_len = turpitude_jenv->GetArrayLength(array);
    if (array_len < 0) 
        php_error(E_ERROR, "invalid array length: %d", array_len);

    // assign jarray to object
    turpitude_javaarray_iterator_object* intern = (turpitude_javaarray_iterator_object*)zend_object_store_get_object(dest TSRMLS_CC);
    intern->java_array = array;

}

