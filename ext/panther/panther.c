/* This file is part of the XP extensions
 *
 * $Id$ 
 */

#include <pthread.h>
#include <stdio.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <main/php.h>
#include <main/SAPI.h>
#include <main/php_main.h>
#include <main/php_variables.h>
#include <main/php_ini.h>
#include <zend_ini.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <unistd.h>
#include "ext/standard/php_var.h"
#include "rmi.h"

#ifndef ZTS
#error Thread safety needs to be enabled
#endif

/* Declare a global mutex */
pthread_mutex_t panther_mutex = PTHREAD_MUTEX_INITIALIZER;

/* Global variables registry */
zval* registry;
void ***tsrm_ls;

/* Condition on which to shutdown */
int panther_shutdown;

/* Global: server socket */
int SD;

#define READ_CHUNK 4096

#define PANTHER_DEBUG    (1<<0L)
#define PANTHER_LOG      (1<<1L)
#define PANTHER_INFO     (1<<2L)
#define PANTHER_WARNING  (1<<3L)
#define PANTHER_ERROR    (1<<4L)
#define PANTHER_FATAL    (1<<5L)

#define panther_free(msg, x) if (x) { panther_error(PANTHER_DEBUG, "Freeing %s\n", msg); free(x); } else { panther_error(PANTHER_DEBUG, "No need to free %s\n", msg); }

/* {{{ panther_error - error handler */
static void panther_error(int type, char* msg, ...)
{    
    va_list ap;
    char *txt;
    
    txt= (char*)malloc(33 + strlen(msg));
    switch (type) {
        case PANTHER_DEBUG:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Debug");
            break;
            
        case PANTHER_LOG:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Log");
            break;

        case PANTHER_INFO:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Information");
            break;
            
        case PANTHER_WARNING:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Warning");
            break;
            
        case PANTHER_ERROR:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Error");
            break;
            
        case PANTHER_FATAL:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "Fatal");
            break;
            
        default:
            snprintf(txt, 33, "panther[%d] %-11s: ", (int)pthread_self(), "(Unknown)");
            break;
    }
    strcat(txt, msg);
    va_start(ap, msg);
    vfprintf(stderr, txt, ap);
    va_end(ap);
    
    free(txt);
    
    if (type == PANTHER_FATAL) exit(-1);
}
/* }}} */

/* {{{ panther_signal - signal handler */
static void panther_signal(int sig) {
    panther_error(PANTHER_WARNING, "Signal %d rec'vd\n", sig);
    panther_shutdown= 1;
    close(SD);
}

/* {{{ PHP module functions */
static char* panther_read_cookies(TSRMLS_D)
{
    return NULL;
}

static int panther_deactivate(TSRMLS_D)
{
    fflush(stdout);
    return SUCCESS;
}

static int panther_ub_write(const char *str, uint str_length TSRMLS_DC)
{
    /* panther_error(PANTHER_LOG, "[SAPI] ub_write (%d)'%s'\n", str_length, str); */
    fprintf(stderr, str);
    return str_length;
}

static void panther_flush(void *server_context)
{
    panther_error(PANTHER_LOG, "[SAPI] flush\n");
}

static void panther_send_header(sapi_header_struct *sapi_header, void *server_context TSRMLS_DC)
{
    if (sapi_header) {
        panther_error(PANTHER_LOG, "[SAPI] send_header (%d)'%s'\n", sapi_header->header_len, sapi_header->header);
    }
}

static void panther_log_message(char *message)
{
    panther_error(PANTHER_LOG, message);
}

static void panther_register_variables(zval *track_vars_array TSRMLS_DC)
{
    php_import_environment_variables(track_vars_array TSRMLS_CC);
}

static int panther_startup(sapi_module_struct *sapi_module)
{
    return php_module_startup(sapi_module, NULL, 0);
}

static void panther_error_cb(int type, const char *error_filename, const uint error_lineno, const char *format, va_list args) {
  char *buffer;
  int buffer_len;
  TSRMLS_FETCH();
  if (!(EG(error_reporting) & type)) return;
  
  buffer_len = vspprintf(&buffer, PG(log_errors_max_len), format, args);
  fprintf(stderr, "*** Error #%d on line %d of %s\n    %s\n", type, error_lineno, error_filename ? error_filename : "(Unknown)", buffer);
  efree(buffer);
}

static sapi_module_struct panther_sapi_module = {
    "panther",                        /* name */
    "panther",                          /* pretty name */
    panther_startup,                    /* startup */
    php_module_shutdown_wrapper,    /* shutdown */
    NULL,                            /* activate */
    panther_deactivate,              /* deactivate */
    panther_ub_write,                /* unbuffered write */
    panther_flush,                    /* flush */
    NULL,                            /* get uid */
    NULL,                            /* getenv */
    php_error,                        /* error handler */
    NULL,                            /* header handler */
    NULL,                           /* send headers handler */
    panther_send_header,                /* send header handler */
    NULL,                            /* read POST data */
    panther_read_cookies,            /* read Cookies */
    panther_register_variables,      /* register server variables */
    panther_log_message,                /* Log message */

    STANDARD_SAPI_MODULE_PROPERTIES
};

void* _thread(void* sd)
{
    int client_sd, cli_len;
    struct sockaddr_in cli_addr;
    char chunk[READ_CHUNK];
    char* request_str;
    char* buffer;
    char* response_str;
    int n;
    rmirequest* request;
    rmiresponse* response;
    
    while (!panther_shutdown) {
        
        /* Accept */
        cli_len= sizeof(cli_addr);
        client_sd= accept((int)sd, (struct sockaddr *) &cli_addr, &cli_len);
        if (client_sd == -1) {
            panther_error(PANTHER_WARNING, "Accept failed [-e:%s] shutdown= %d\n", strerror(errno), panther_shutdown);
            continue;
        }
        
        panther_error(PANTHER_INFO, "Accepted client %u\n", client_sd);
        
        /* Get request data */
        while (1) {
            errno= 0;
            
            panther_error(PANTHER_INFO, "Receiving from client %u\n", client_sd);
            n= recv(client_sd, chunk, READ_CHUNK, 0);
            if (n <= 0) {
                panther_error(PANTHER_WARNING, "Recv failed (%d bytes) [-e:%s]\n", n, n == 0 ? "n/a" : strerror(errno));
                break;
            }
            
            buffer= (char*) malloc(n);
            strlcpy(buffer, chunk, n+ 1);
            while ((request_str= strsep(&buffer, "\n"))) {
                if (0 == (n= strlen(request_str))) continue;
                request_str[n- 1]= '\0';
                panther_error(PANTHER_LOG, "Will handle: (%d)'%s'\n", n, request_str);

                /* Parse request */
                if ((n= parserequest(request_str, &request)) != RMI_SUCCESS) {
                    panther_error(PANTHER_WARNING, "Could not parse request '%s', errno= %d\n", request_str, n);
                    break;
                }
                panther_error(
                  PANTHER_INFO,
                  "rmirequest {\n  method= %d\n  length= %d\n  class= (%d)'%s'\n  member= (%d)'%s'\n  data= (%d)'%s'\n}\n",
                  request->method,
                  request->length,
                  request->class_len,
                  request->class,
                  request->member_len,
                  request->member,
                  request->data_len,
                  request->data
                );

                /* Set up response */
                response= malloc(sizeof(rmiresponse));
                response->method= request->method;

                /* Execute */
                zend_first_try {
                    zval *object = NULL;
                    
                    if (FAILURE == zend_hash_find(Z_ARRVAL_P(registry), request->class, request->class_len + 1, (void**) &object)) {
                        panther_error(PANTHER_WARNING, "Could not find class (%d)'%s' in registry\n", request->class_len, request->class);
                        
                        response->method= RMI_EXCEPTION;
                        response->length= sizeof("O:21:\"nosuchobjectexception\":1:{s:7:\"message\";s:  :\"No such object  registered\";};") + request->class_len - 1;
                        response->data= (char*)malloc(response->length + 1);
                        snprintf(response->data, response->length + 1, "O:21:\"nosuchobjectexception\":1:{s:7:\"message\";s:%d:\"No such object %s registered\";};", request->class_len + 26, request->class);
                    } else {
                        char *eval;
                        zval retval;
                        zval *retval_ptr;
                        zval *data_ptr = NULL;
                        php_serialize_data_t s_hash;
                        php_unserialize_data_t u_hash;
                        const char *p;
                        smart_str buf = {0};

                        switch (request->method) {
                            case RMI_SET:
                                p= request->data;
                                PHP_VAR_UNSERIALIZE_INIT(u_hash);
                                ALLOC_ZVAL(data_ptr);
                                php_var_unserialize(&data_ptr, &p, p + request->data_len, &u_hash TSRMLS_CC);
                                PHP_VAR_UNSERIALIZE_DESTROY(u_hash);
                                ZEND_SET_SYMBOL(&EG(symbol_table), "_", data_ptr);

                                n= sizeof("$registry['']->= $_") + request->class_len + request->member_len + request->data_len;
                                eval= (char*) emalloc(n);
                                strncpy(eval, "$registry['", sizeof("$registry['"));
                                strncat(eval, request->class, request->class_len);
                                strncat(eval, "']->", sizeof("']->"));
                                strncat(eval, request->member, request->member_len);
                                strncat(eval, "= $_", sizeof("= $_"));
                                break;

                            case RMI_GET:
                                n= sizeof("$registry['']->") + request->class_len + request->member_len;
                                eval= (char*) emalloc(n);
                                strncpy(eval, "$registry['", sizeof("$registry['"));
                                strncat(eval, request->class, request->class_len);
                                strncat(eval, "']->", sizeof("']->"));
                                strncat(eval, request->member, request->member_len);
                                break;

                            case RMI_INVOKE:
                                p= request->data;
                                PHP_VAR_UNSERIALIZE_INIT(u_hash);
                                ALLOC_ZVAL(data_ptr);
                                php_var_unserialize(&data_ptr, &p, p + request->data_len, &u_hash TSRMLS_CC);
                                PHP_VAR_UNSERIALIZE_DESTROY(u_hash);

                                ZEND_SET_SYMBOL(&EG(symbol_table), "_", data_ptr);

                                n= sizeof("$registry['']->($_)") + request->class_len + request->member_len;
                                eval= (char*) emalloc(n);
                                strncpy(eval, "$registry['", sizeof("$registry['"));
                                strncat(eval, request->class, request->class_len);
                                strncat(eval, "']->", sizeof("']->"));
                                strncat(eval, request->member, request->member_len);
                                strncat(eval, "($_)", sizeof("($_)"));
                                break;

                            default:
                                eval= (char*) emalloc(0);    /* Shut up, GCC */
                        }

                        panther_error(PANTHER_LOG, "Executing (%d)'%s'\n", strlen(eval), eval);
                        if (FAILURE == zend_eval_string(eval, &retval, "panther.php" TSRMLS_CC)) {
                            panther_error(PANTHER_WARNING, "zend_eval_string('%s') returns FAILURE", eval);

                            response->method= RMI_EXCEPTION;
                            response->length= sizeof("N;") - 1;
                            response->data= (char*)malloc(response->length + 1);
                            strlcpy(response->data, "N;", response->length + 1);
                        } else {
                            retval_ptr= &retval;
                            zval_copy_ctor(retval_ptr);

                            if (retval_ptr) {
                                php_var_dump(&retval_ptr, 0 TSRMLS_CC);

                                PHP_VAR_SERIALIZE_INIT(s_hash);
                                php_var_serialize(&buf, &retval_ptr, &s_hash TSRMLS_CC);
                                PHP_VAR_SERIALIZE_DESTROY(s_hash);

                                response->length= buf.len;
                                response->data= (char*)malloc(response->length + 1);
                                strlcpy(response->data, buf.c, response->length + 1);
                                zval_ptr_dtor(&retval_ptr); 
                            } else {
                                response->length= sizeof("N;") - 1;
                                response->data= (char*)malloc(response->length + 1);
                                strlcpy(response->data, "N;", response->length + 1);
                            }
                        }
                        efree(eval);
                    }
                    
                } zend_end_try();
                
                panther_error(
                    PANTHER_INFO,
                    "rmiresponse {\n  method= %d\n  length= %d\n  data= (%d)'%s'\n}\n",
                    response->method,
                    response->length,
                    strlen(response->data),
                    response->data
                );
                responsestring(response, &response_str, &n);

                panther_error(PANTHER_LOG, "Sending (%d)'%s'\n", n, response_str);
                send(client_sd, response_str, n, 0);
                send(client_sd, "\r\n", sizeof("\r\n"), MSG_EOR);

                /* Clean up */
                panther_free("request", request);
                
                panther_free("response_str", response_str);
                panther_free("response->data", response->data);
                panther_free("response", response);
            }
            
            panther_free("buffer", buffer);
        }
       
        panther_error(PANTHER_INFO, "Closing communications with client %u\n", client_sd);
        close(client_sd);
    }
    
    pthread_exit(NULL);
}

/* {{{ main */
int main (int argc, char *argv[])
{
    pthread_t* threads= NULL;
    int rc, t, opt;
    void* status;
    struct sockaddr_in serv_addr;
    int max_threads;
    int serverport;
    zend_file_handle fh;
    
    serverport= (argc > 1) ? atoi(argv[1]) : 8080;
    max_threads= (argc > 2) ? atoi(argv[2]) : 2;
    
    /* Create socket */
    SD= socket(AF_INET, SOCK_STREAM, 0);
    if (SD < 0) {
        panther_error(PANTHER_FATAL, "Cannot create socket\n");
    }
    
    serv_addr.sin_family= AF_INET;
    serv_addr.sin_addr.s_addr= htonl(INADDR_ANY);
    serv_addr.sin_port= htons(serverport);

    setsockopt(SD, SOL_SOCKET, SO_REUSEPORT, &opt, sizeof(opt));
    if (bind(SD, (struct sockaddr *) &serv_addr, sizeof(serv_addr)) < 0) {
        panther_error(PANTHER_FATAL, "Cannot bind port %d\n", serverport);
    }

    if (listen(SD, 1)) {
        panther_error(PANTHER_FATAL, "Cannot listen on socket descriptor\n");
    }    
    
    /* Setup signal handler */
    signal(SIGINT, panther_signal);
    signal(SIGPIPE, SIG_IGN);
  
    /* Startup PHP module */
    tsrm_startup(1, 1, 0, NULL);
    tsrm_ls = ts_resource(0);
    sapi_startup(&panther_sapi_module);
    if (SUCCESS != php_module_startup(&panther_sapi_module, NULL, 0)) {
        panther_error(PANTHER_FATAL, "Cannot startup SAPI module\n");
    }
    if (SUCCESS != php_request_startup(TSRMLS_C)) {
        panther_error(PANTHER_FATAL, "Cannot startup request\n");
    }

    /* Set error callback */
    zend_error_cb= panther_error_cb;

    fh.filename= "panther.php";
    fh.opened_path= NULL;
    fh.free_filename= 0;
    fh.type= ZEND_HANDLE_FILENAME;

    /* This is where the execution begins */
    panther_error(PANTHER_LOG, "Execute [%d]%s...\n", strlen(fh.filename), fh.filename);
    if (FAILURE == zend_execute_scripts(ZEND_INCLUDE TSRMLS_CC, &registry, 1, &fh)) {
        panther_error(PANTHER_FATAL, "Internal error: Could not execute init script\n");
        /* Bails out */
    }
    
    php_var_dump(&registry, 0 TSRMLS_CC);
    
    for (t= 0; t < max_threads; t++) {

        /* Allocate some more memory and create a new thread */
        panther_error(PANTHER_LOG, "Creating thread %d\n", t, sizeof(pthread_t) * (t + 1));        
        threads= (pthread_t*)realloc(threads, sizeof(pthread_t) * (t + 1)); 
        if (!threads) {
            panther_error(PANTHER_FATAL, "Out of memory in realloc() - tried to allocate %d bytes", sizeof(pthread_t));
        }
        if ((rc= pthread_create(&threads[t], NULL, _thread, (void *)SD))) {
            panther_error(PANTHER_FATAL, "Cannot create thread, return code %d\n", rc);
        }
    }
    
    panther_error(PANTHER_INFO, "Waiting...\n");
    
    /* Join all remaining threads */
    while (t > 0) {
        t--;
        if ((rc= pthread_join(threads[t], (void **)&status))) {
            panther_error(PANTHER_WARNING, "Could not join thread, return code %d\n", rc);
        }
        panther_error(PANTHER_LOG, "Completed join with thread %d status= %x\n", t, status);
    }

    /* Free up memory */
    panther_free("threads", threads);
    zval_ptr_dtor(&registry);

    /* Shutdown PHP module */
    php_request_shutdown((void *) 0);
    ts_free_id(0);
    php_module_shutdown(TSRMLS_C);
    tsrm_shutdown();
    
    pthread_exit(NULL);
}
/* }}} */
