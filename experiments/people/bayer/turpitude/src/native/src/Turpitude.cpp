#include <Turpitude.h>

std::string LastError = "no error";

int turpitude_startup(sapi_module_struct* sapi_module) {
    return php_module_startup(sapi_module, NULL, 0);
}

char* turpitude_read_cookies(TSRMLS_D) {
    //no cookies to read
    return NULL;
}

int turpitude_deactivate(TSRMLS_D) {
    fflush(stdout);
    return SUCCESS;
}

int turpitude_ub_write(const char *str, uint str_length TSRMLS_DC) {
    printf(str);
    return str_length;
}

void turpitude_flush(void* server_context) {
}

int turpitude_send_headers(sapi_headers_struct *sapi_headers TSRMLS_DC) {
    //no headers to be sent 
    return SAPI_HEADER_SENT_SUCCESSFULLY;
}

void turpitude_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC) {
    //no headers to be sent 
}

void turpitude_log_message(char *message) {
    fprintf(stderr, message);
}

void turpitude_register_variables(zval *track_vars_array TSRMLS_DC) {
    php_import_environment_variables(track_vars_array TSRMLS_CC);
}

void turpitude_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
    char *buffer;
    int buffer_len;
    TSRMLS_FETCH();
    if (!(EG(error_reporting) & type)) return;

    buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
    //fprintf(stderr, "*** Error #%d on line %d of %s\n    %s\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);

    LastError += "\n";
    LastError += buffer;
    efree(buffer);
    switch (type) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_USER_ERROR:
            zend_bailout();
            break;
    }

}

jobject zval_to_jobject(JNIEnv* env, zval* val) {

    jclass cls = NULL;
    jobject obj = NULL;
    jmethodID mid = NULL;

    switch (Z_TYPE_P(val)) {
        case IS_LONG:
            cls = env->FindClass("java/lang/Long");
            mid = env->GetMethodID(cls, "<init>", "(J)V");
            obj = env->NewObject(cls, mid, val->value.lval);
            break;
        case IS_DOUBLE:
            printf("IS_DOUBLE\n");
            cls = env->FindClass("java/lang/Double");
            mid = env->GetMethodID(cls, "<init>", "(D)V");
            obj = env->NewObject(cls, mid, val->value.dval);
            break;
        case IS_BOOL:
            printf("IS_BOOL\n");
            cls = env->FindClass("java/lang/Boolean");
            mid = env->GetMethodID(cls, "<init>", "(Z)V");
            obj = env->NewObject(cls, mid, (val->value.lval)?true:false);
            break;
        case IS_ARRAY:
            printf("IS_ARRAY\n");
            break;
        case IS_OBJECT:
            printf("IS_OBJECT\n");
            break;
        case IS_CONSTANT:
        case IS_STRING:
            obj = env->NewStringUTF(val->value.str.val);
            break;
        default:
            // probably null
            obj = NULL;
    }

    return obj;
}

/* {{{ PHP SAPI registry */
sapi_module_struct turpitude_sapi_module = {
    "turpitude",                              /* name */
    "Turpitude Java PHP Executor",            /* pretty name */

    turpitude_startup,                        /* startup */
    php_module_shutdown_wrapper,              /* shutdown */

    NULL,                                     /* activate */
    turpitude_deactivate,                     /* deactivate */

    turpitude_ub_write,                       /* unbuffered write */
    turpitude_flush,                          /* flush */
    NULL,                                     /* get uid */
    NULL,                                     /* getenv */

    php_error,                                /* error handler */

    NULL,                                     /* header handler */
    turpitude_send_headers,                   /* send headers handler */
    turpitude_send_header,                    /* send header handler */

    NULL,                                     /* read POST data */
    turpitude_read_cookies,                   /* read Cookies */

    turpitude_register_variables,             /* register server variables */
    turpitude_log_message,                    /* Log message */

    NULL,                                     /* Block interruptions */
    NULL,                                     /* Unblock interruptions */

    STANDARD_SAPI_MODULE_PROPERTIES
};
/* }}} */


