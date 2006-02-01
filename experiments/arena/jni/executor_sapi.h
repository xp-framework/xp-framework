#ifndef EXECUTOR_SAPI_H
#define EXECUTOR_SAPI_H

#include <main/php.h>
#include <main/SAPI.h>
#include <main/php_main.h>
#include <main/php_variables.h>
#include <main/php_ini.h>
#include <zend_ini.h>
#include <zend_errors.h>
#include <zend_compile.h>
#include <zend_execute.h>

#include "util.h"

/* {{{ PHP module functions */
char* phpexecutor_read_cookies(TSRMLS_D);
int phpexecutor_deactivate(TSRMLS_D);
int phpexecutor_ub_write(const char *str, uint str_length TSRMLS_DC);
void phpexecutor_flush(void *server_context);
int phpexecutor_send_headers(sapi_headers_struct *sapi_headers TSRMLS_DC);
void phpexecutor_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC);
void phpexecutor_log_message(char *message);
void phpexecutor_register_variables(zval *track_vars_array TSRMLS_DC);
int phpexecutor_startup(sapi_module_struct *sapi_module);
void phpexecutor_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args);
/* }}} */

/* {{{ PHP SAPI registry */
extern sapi_module_struct phpexecutor_sapi_module;
/* }}} */

#endif
