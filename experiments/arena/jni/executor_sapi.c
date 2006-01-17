#include "executor_sapi.h"
#include <stdio.h>

/* {{{ PHP module functions */
char* phpexecutor_read_cookies(TSRMLS_D) {
    return NULL;
}

int phpexecutor_deactivate(TSRMLS_D) {
    fflush(stdout);
    return SUCCESS;
}

int phpexecutor_ub_write(const char *str, uint str_length TSRMLS_DC) {
    /* DEBUG fprintf(stderr, "[SAPI] ub_write (%d)'%s'\n", str_length, str); */
    printf(str);
    return str_length;
}

void phpexecutor_flush(void *server_context) {
    fprintf(stderr, "[SAPI] flush\n");
}

int phpexecutor_send_headers(sapi_headers_struct *sapi_headers TSRMLS_DC) {
	return SAPI_HEADER_SENT_SUCCESSFULLY;
}

void phpexecutor_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC) {
}

void phpexecutor_log_message(char *message) {
    fprintf(stderr, message);
}

void phpexecutor_register_variables(zval *track_vars_array TSRMLS_DC) {
    php_import_environment_variables(track_vars_array TSRMLS_CC);
}

int phpexecutor_startup(sapi_module_struct *sapi_module) {
    return php_module_startup(sapi_module, NULL, 0);
}

void phpexecutor_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
    char *buffer;
    int buffer_len;
    TSRMLS_FETCH();
    if (!(EG(error_reporting) & type)) return;
  
    buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
    fprintf(stderr, "*** Error #%d on line %d of %s\n    %s\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);
    efree(buffer);
    
	switch (type) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_USER_ERROR:
            zend_bailout();
            break;
    }
}
/* }}} */

/* {{{ PHP SAPI registry */
sapi_module_struct phpexecutor_sapi_module = {
    "phpexecutor",                            /* name */
    "Java PHP Executor",                      /* pretty name */

    phpexecutor_startup,                      /* startup */
    php_module_shutdown_wrapper,              /* shutdown */

    NULL,                                     /* activate */
    phpexecutor_deactivate,                   /* deactivate */

    phpexecutor_ub_write,                     /* unbuffered write */
    phpexecutor_flush,                        /* flush */
    NULL,                                     /* get uid */
    NULL,                                     /* getenv */

    php_error,                                /* error handler */

    NULL,                                     /* header handler */
    phpexecutor_send_headers,                 /* send headers handler */
    phpexecutor_send_header,                  /* send header handler */

    NULL,                                     /* read POST data */
    phpexecutor_read_cookies,                 /* read Cookies */

    phpexecutor_register_variables,           /* register server variables */
    phpexecutor_log_message,                  /* Log message */

	NULL,							          /* Block interruptions */
	NULL,							          /* Unblock interruptions */

    STANDARD_SAPI_MODULE_PROPERTIES
};
/* }}} */
