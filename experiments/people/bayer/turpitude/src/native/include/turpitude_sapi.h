#ifndef __TURPITUDE_SAPI_H__
#define __TURPITUDE_SAPI_H__

#include <main/php.h>
#include <main/SAPI.h>
#include <main/php_main.h>
#include <main/php_variables.h>
#include <main/php_ini.h>
#include <zend_ini.h>
#include <zend_errors.h>
#include <zend_compile.h>
#include <zend_execute.h>
#include <jni.h>
#include <string>


/* PHP module functions */
int turpitude_startup(sapi_module_struct* sapi_module);
char* turpitude_read_cookies(TSRMLS_D);
int turpitude_deactivate(TSRMLS_D);
int turpitude_ub_write(const char *str, uint str_length TSRMLS_DC);
void turpitude_flush(void* server_context);
int turpitude_send_headers(sapi_headers_struct *sapi_headers TSRMLS_DC);
void turpitude_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC);
void turpitude_log_message(char *message);
void turpitude_register_variables(zval *track_vars_array TSRMLS_DC);
void turpitude_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args);

/* PHP SAPI registry */
extern sapi_module_struct turpitude_sapi_module;

static void setErrorMsg(const char* msg);
static void resetErrorMsg();

/* last error string */
extern std::string LastError;
extern bool ErrorCBCalled;

inline void set_zend_globals() {
    zend_error_cb= turpitude_error_cb;
    zend_uv.html_errors= 0;
    CG(in_compilation)= 0;
    CG(interactive)= 0;
    EG(uninitialized_zval_ptr)= NULL;
    EG(error_reporting)= E_ALL;

    INIT_ZVAL(EG(uninitialized_zval));
    EG(uninitialized_zval).refcount++;
    INIT_ZVAL(EG(error_zval));
    EG(uninitialized_zval_ptr)=&EG(uninitialized_zval);
    EG(error_zval_ptr)=&EG(error_zval);
}
#endif
