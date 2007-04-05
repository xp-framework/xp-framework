/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"
#include "sybase_mm.h"
#include "sybase_hash.h"
#include "sybase_defines.h"

/**
 * Setup environment
 * 
 * @access  public
 * @param   sybase_environment **env
 * @return  int
 */
SYBASE_API int sybase_init(sybase_environment **env)
{   
    *env= (sybase_environment *) smalloc(sizeof(sybase_environment));
    if (!*env) {
        return SA_FAILURE | SA_EALLOC;
    }
    
    if (cs_ctx_alloc(CTLIB_VERSION, &(*env)->context) != CS_SUCCEED) {
        sfree(*env);
        return SA_FAILURE | SA_EALLOC | SA_ECTLIB;
    }
    if (ct_init((*env)->context, CTLIB_VERSION) != CS_SUCCEED) {
        sfree(*env);
        return SA_FAILURE | SA_EALLOC | SA_ECTLIB;
    }
    return SA_SUCCESS;
}

/**
 * Set a message handler
 * 
 * The declaration for the messagehandler is the following:
 *
 * <pre>
 * 1) Server message handler:
 *    =======================
 *    static CS_RETCODE CS_PUBLIC servermessage(
 *      CS_CONTEXT *context, 
 *      CS_CONNECTION *connection, 
 *      CS_SERVERMSG *message
 *    )
 *
 *    Server message callbacks must return CS_SUCCEED.
 *
 * 2) Client message handler:
 *    =======================
 *    static CS_RETCODE CS_PUBLIC clientmessage(
 *      CS_CONTEXT *context, 
 *      CS_CONNECTION *connection, 
 *      CS_CLIENTMSG *message
 *    )
 *
 *    If we return CS_FAIL, Client-Library marks the connection
 *    as dead. This means that it cannot be used anymore.
 *    If we return CS_SUCCEED, the connection remains alive
 *    if it was not already dead.
 * </pre>
 *
 * @access  public
 * @param   sybase_environment *env
 * @param   int type CS_SERVERMSG_CB or CS_CLIENTMSG_CB
 * @param   CS_VOID *handler
 * @return  int
 */
SYBASE_API int sybase_set_messagehandler(sybase_environment *env, int type, CS_VOID *handler)
{
    if (ct_callback(env->context, NULL, CS_SET, type, (CS_VOID *)handler) != CS_SUCCEED) {
        return SA_FAILURE | SA_ECTLIB;
    }    
    return SA_SUCCESS;
}

/**
 * Shutdown environment
 * 
 * @access  public
 * @param   sybase_environment *env
 * @return  int
 */
SYBASE_API int sybase_shutdown(sybase_environment *env)
{
    if (!env) {
        return SA_FAILURE | SA_ENULLPOINTER;
    }
    ct_exit(env->context, CS_UNUSED);
    cs_ctx_drop(env->context);
    sfree(env);
    return SA_SUCCESS;
}

/**
 * Allocate a connection
 * 
 * @access  public
 * @param   sybase_link **link
 * @return  int
 */
SYBASE_API int sybase_alloc(sybase_link **link)
{
    *link= (sybase_link *) smalloc(sizeof(sybase_link));
    if (!*link) {
        return SA_FAILURE | SA_EALLOC;
    }
    return SA_SUCCESS;
}

/**
 * Connect to the database
 * 
 * @access  public
 * @param   sybase_environment *env the environment previously initialized with sybase_init
 * @param   char *host
 * @param   char *user Username, may be NULL
 * @param   char *pass Password, may be NULL
 * @param   sybase_hash *props Connection properties hash, may be NULL
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_connect(sybase_environment *env, sybase_link *link, char *host, char *user, char *pass, sybase_hash *props)
{
    if (ct_con_alloc(env->context, &link->connection) != CS_SUCCEED) {
        return SA_FAILURE | SA_EALLOC;
    }

    /* Set username and password */
    if (user) {
        ct_con_props(link->connection, CS_SET, CS_USERNAME, user, CS_NULLTERM, NULL);
    }
    if (pass) {
        ct_con_props(link->connection, CS_SET, CS_PASSWORD, pass, CS_NULLTERM, NULL);
    }
    
    /* Set extended connection properties */
    if (props) {
        sybase_hash_element *e;
        int c;
        
        for (e= sybase_hash_first(props, &c); sybase_hash_has_more(props, c); e= sybase_hash_next(props, &c)) {
            switch (e->type) {
                case HASH_STRING:
                    ct_con_props(link->connection, CS_SET, e->key, e->value.str.val, CS_NULLTERM, NULL);
                    break;
                case HASH_INT:
                    ct_con_props(link->connection, CS_SET, e->key, &e->value.lval, CS_UNUSED, NULL);
                    break;
            }
        }
    }

    if (ct_connect(link->connection, host, CS_NULLTERM) != CS_SUCCEED) {
        ct_con_drop(link->connection);
        return SA_FAILURE | SA_ECTLIB;
    }

    return SA_SUCCESS;
}

/**
 * Retrieve connection status. Assumes CS_CONSTAT_DEAD when retrieving the 
 * connection status fails.
 * 
 * @access  public
 * @param   sybase_link *link
 * @return  CS_INT a bitfield of CS_CONSTAT_CONNECTED and CS_CONSTAT_DEAD
 */
SYBASE_API CS_INT sybase_connection_status(sybase_link *link)
{
    CS_INT status;
    
    if (ct_con_props(link->connection, CS_GET, CS_CON_STATUS, &status, CS_UNUSED, NULL) != CS_SUCCEED) {
        status= CS_CONSTAT_DEAD;
    }
    
    return status;
}

/**
 * Check if whe are connected
 * 
 * @access  public
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_is_connected(sybase_link *link)
{
    return (sybase_connection_status(link) & CS_CONSTAT_CONNECTED) ? SA_SUCCESS : SA_FAILURE;
}

/**
 * Send a command
 * 
 * @access  public
 * @param   sybase_link *link
 * @param   sybase_result **result
 * @param   char *query
 * @return  int
 */
SYBASE_API int sybase_query(sybase_link *link, sybase_result **result, char *query)
{
    *result= (sybase_result*) smalloc(sizeof(sybase_result));
    if (!*result) {
       return SA_FAILURE | SA_EALLOC; 
    }
    if (ct_cmd_alloc(link->connection, &(*result)->cmd) != CS_SUCCEED) {
        return SA_FAILURE | SA_EALLOC;
    }
    if (ct_command((*result)->cmd, CS_LANG_CMD, query, CS_NULLTERM, CS_UNUSED) != CS_SUCCEED) {
        return SA_FAILURE;
    }
    if (ct_send((*result)->cmd) != CS_SUCCEED) {
        return SA_FAILURE;
    }
    return SA_SUCCESS;
}

/**
 * Cancel a command
 * 
 * @access  public
 * @param   sybase_result *result
 * @param   int mode CS_CANCEL_ALL, CS_CANCEL_ATTN or CS_CANCEL_CURRENT
 * @return  int
 */
SYBASE_API int sybase_cancel(sybase_result *result, int mode)
{
    if (ct_cancel(NULL, result->cmd, mode) != CS_SUCCEED) {
        return SA_FAILURE;
    }
    return SA_SUCCESS;
}

/**
 * Retrieve rowcount
 * 
 * @access  public
 * @param   sybase_result *result
 * @return  int the number of affected rows or -1 to indicate failure
 */
SYBASE_API int sybase_rowcount(sybase_result *result)
{
    CS_INT rowcount;

    if (ct_res_info(result->cmd, CS_ROW_COUNT, &rowcount, CS_UNUSED, NULL) != CS_SUCCEED) {
        return -1;
    }
    return rowcount;
}

/**
 * Read a result
 * 
 * @access  public
 * @param   sybase_result **result
 * @return  int
 */
SYBASE_API int sybase_results(sybase_result **result)
{
    (*result)->code= ct_results((*result)->cmd, &(*result)->type);
    return ((*result)->code == CS_SUCCEED) ? SA_SUCCESS : SA_FAILURE;
}

/**
 * Calculate length of a given datatype for representation as a 
 * nulltermimated string.
 * 
 * @access  private
 * @param   CS_DATAFMT f
 * @return  int
 */
static inline int _sybase_lengthof(CS_DATAFMT f)
{
    int len;

    len= f.maxlength;
    switch (f.datatype) {
        case CS_BINARY_TYPE:
        case CS_VARBINARY_TYPE:
            len*= 2;
            len++;
            break;

        case CS_BIT_TYPE:
        case CS_TINYINT_TYPE:
            len= 4;
            break;

        case CS_SMALLINT_TYPE:
            len= 7;
            break;

        case CS_INT_TYPE:
            len= 12;
            break;

        case CS_REAL_TYPE:
        case CS_FLOAT_TYPE:
            len= 24;
            break;

        case CS_MONEY_TYPE:
        case CS_MONEY4_TYPE:
            len= 24;
            break;

        case CS_DATETIME_TYPE:
        case CS_DATETIME4_TYPE:
            len= 30;
            break;

        case CS_NUMERIC_TYPE:
        case CS_DECIMAL_TYPE:
            len= f.precision + 3;
            break;

        case CS_CHAR_TYPE:
        case CS_VARCHAR_TYPE:
        case CS_TEXT_TYPE:
        case CS_IMAGE_TYPE:
        default:
            len++;
            break;
    }
    return len;
}

/**
 * Initialize a resultset
 * 
 * @access  public
 * @param   sybase_result **result
 * @param   sybase_resultset **resultset
 * @return  int
 */
SYBASE_API int sybase_init_resultset(sybase_result *result, sybase_resultset **resultset)
{
    int fields, i, len;
    
    if (ct_res_info(result->cmd, CS_NUMDATA, &fields, CS_UNUSED, NULL) != CS_SUCCEED) {
        return SA_FAILURE;
    }

    *resultset= (sybase_resultset*) smalloc(sizeof(sybase_result));
    if (!*resultset) {
       return SA_FAILURE | SA_EALLOC; 
    }
    (*resultset)->dataformat = (CS_DATAFMT *) smalloc(sizeof(CS_DATAFMT) * fields);
    if (!(*resultset)->dataformat) {
       return SA_FAILURE | SA_EALLOC; 
    }
    (*resultset)->columns = (sybase_column *) smalloc(sizeof(sybase_column) * fields);
    if (!(*resultset)->columns) {
       return SA_FAILURE | SA_EALLOC; 
    }
    (*resultset)->types = (CS_INT *) smalloc(sizeof(CS_INT) * fields);
    if (!(*resultset)->types) {
       return SA_FAILURE | SA_EALLOC; 
    }
    
    (*resultset)->fields= fields;
    for (i= 0; i < fields; i++) {
        ct_describe(result->cmd, i + 1, &(*resultset)->dataformat[i]);

        /* Just to be sure, make name null-terminated */
        (*resultset)->dataformat[i].name[(*resultset)->dataformat[i].namelen]= '\0';
        
        /* Calculate length needed for this value to be represented as
         * a null-terminated string */
        len= _sybase_lengthof((*resultset)->dataformat[i]);
        
        /* Change CS_DATAFMT setting datatype to CS_CHAR_TYPE and format 
         * to CS_FMT_NULLTERM. Store the original type in types */
        (*resultset)->types[i]= (*resultset)->dataformat[i].datatype;
        (*resultset)->dataformat[i].datatype= CS_CHAR_TYPE;
        (*resultset)->dataformat[i].format = CS_FMT_NULLTERM;
        (*resultset)->dataformat[i].maxlength = len;
        
        (*resultset)->columns[i].value= (char *) smalloc(len);
        ct_bind(
            result->cmd, 
            i + 1, 
            &(*resultset)->dataformat[i], 
            (*resultset)->columns[i].value, 
            &(*resultset)->columns[i].valuelen, 
            &(*resultset)->columns[i].indicator
        );
    }
    return SA_SUCCESS;
}

/**
 * Free a resultset
 * 
 * @access  public
 * @param   sybase_resultset *result
 * @return  int
 */
SYBASE_API int sybase_free_resultset(sybase_resultset *resultset)
{
    int i;

    if (!resultset) {
        return SA_FAILURE | SA_EALREADYFREE;
    }
    for (i= 0; i < resultset->fields; i++) {
        sfree(resultset->columns[i].value);
    }
    sfree(resultset->dataformat);
    sfree(resultset->columns);
    sfree(resultset->types);
    sfree(resultset);
    return SA_SUCCESS;
}

/**
 * Fetch one row
 * 
 * @access  public
 * @param   sybase_resultset *result
 * @param   sybase_resultset **resultset
 * @return  int
 */
SYBASE_API int sybase_fetch(sybase_result *result, sybase_resultset **resultset)
{
    int code, i;
    
    code= ct_fetch(result->cmd, CS_UNUSED, CS_UNUSED, CS_UNUSED, NULL);
    #if 0
    fprintf(stderr, "sybase_fetch(): retcode= %d [%s]\n", code, sybase_nameofcode(code));
    #endif
    switch (code) {
        case CS_SUCCEED:
            break;
        case CS_END_DATA:
            return SA_FAILURE;
        default:
            return SA_FAILURE | SA_ECTLIB;
    }

    /* Set fields to NULL if a NULL value is indicated */
    for (i= 0; i < (*resultset)->fields; i++) {
        if ((*resultset)->columns[i].indicator == CS_NULLDATA) {
            (*resultset)->columns[i].value= NULL;
        }
    }
    return SA_SUCCESS;
}

/**
 * Free a result
 * 
 * @access  public
 * @param   sybase_result *result
 * @return  int
 */
SYBASE_API int sybase_free_result(sybase_result *result)
{
    if (!result) {
        return SA_FAILURE | SA_EALREADYFREE;
    }
    ct_cmd_drop(result->cmd);
    sfree(result);
    return SA_SUCCESS;
}

/**
 * Close the connection to the database
 * 
 * @access  public
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_close(sybase_link *link)
{
    CS_INT status;

    if (!link) {
        return SA_FAILURE | SA_ENULLPOINTER;
    }
    
    /* Only close connection if we are actually connected. 
     * Don't take any chances: Use CS_FORCE_CLOSE for dead connections! 
     */
    status= sybase_connection_status(link);
    if (status & CS_CONSTAT_CONNECTED) {
        ct_close(link->connection, (status & CS_CONSTAT_DEAD) ? CS_FORCE_CLOSE : CS_UNUSED);
        ct_con_drop(link->connection);
        return SA_SUCCESS;
    }
    
    return SA_FAILURE;
}

/**
 * Free the link
 *
 * @access  public
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_free(sybase_link *link)
{
    if (!link) {
        return SA_FAILURE | SA_EALREADYFREE;
    }
    sfree(link);
    return SA_SUCCESS;
}

/**
 * Return the name of a return type
 *
 * @access  public
 * @param   CS_INT type
 * @return  char* name or (UNKNOWN) if the type is unknown
 */
SYBASE_API char *sybase_nameoftype(CS_INT type)
{
    switch ((int)type) {
        case CS_CMD_SUCCEED: 
            return "CS_CMD_SUCCEED";
        case CS_CMD_DONE: 
            return "CS_CMD_DONE";
        case CS_CMD_FAIL: 
            return "CS_CMD_FAIL";
        case CS_COMPUTE_RESULT: 
            return "CS_COMPUTE_RESULT";
        case CS_CURSOR_RESULT: 
            return "CS_CURSOR_RESULT";
        case CS_PARAM_RESULT: 
            return "CS_PARAM_RESULT";
        case CS_ROW_RESULT : 
            return "CS_ROW_RESULT";
        case CS_STATUS_RESULT : 
            return "CS_STATUS_RESULT";
        case CS_COMPUTEFMT_RESULT: 
            return "CS_COMPUTEFMT_RESULT";
        case CS_ROWFMT_RESULT: 
            return "CS_ROWFMT_RESULT";
        case CS_MSG_RESULT: 
            return "CS_MSG_RESULT";
        case CS_DESCRIBE_RESULT: 
            return "CS_DESCRIBE_RESULT";
        default:
            return "(UNKNOWN)";
    }
    /* This point will never be reached */
}

/**
 * Return the name of a return code
 *
 * @access  public
 * @param   CS_INT type
 * @return  char* name or (UNKNOWN) if the type is unknown
 */
SYBASE_API char *sybase_nameofcode(CS_INT code)
{
    switch ((int)code) {
        case CS_SUCCEED: 
            return "CS_SUCCEED";
        case CS_END_RESULTS: 
            return "CS_END_RESULTS";
        case CS_END_DATA: 
            return "CS_END_DATA";
        case CS_FAIL: 
            return "CS_FAIL";
        case CS_ROW_FAIL:
            return "CS_ROW_FAIL";
        case CS_CANCELED: 
            return "CS_CANCELED";
        default:
            return "(UNKNOWN)";
    }
    /* This point will never be reached */
}

/**
 * Return the name of a data type
 *
 * @access  public
 * @param   CS_INT type
 * @return  char* name or (UNKNOWN) if the type is unknown
 */
SYBASE_API char *sybase_nameofdatatype(CS_INT datatype)
{
    switch ((int)datatype) {
        case CS_CHAR_TYPE:
            return "CS_CHAR_TYPE";
        case CS_INT_TYPE:
            return "CS_INT_TYPE";
        case CS_SMALLINT_TYPE:
            return "CS_SMALLINT_TYPE";
        case CS_TINYINT_TYPE:
            return "CS_TINYINT_TYPE";
        case CS_MONEY_TYPE:
            return "CS_MONEY_TYPE";
        case CS_DATETIME_TYPE:
            return "CS_DATETIME_TYPE";
        case CS_NUMERIC_TYPE:
            return "CS_NUMERIC_TYPE";
        case CS_DECIMAL_TYPE:
            return "CS_DECIMAL_TYPE";
        case CS_DATETIME4_TYPE:
            return "CS_DATETIME4_TYPE";
        case CS_MONEY4_TYPE:
            return "CS_MONEY4_TYPE";
        case CS_IMAGE_TYPE:
            return "CS_IMAGE_TYPE";
        case CS_BINARY_TYPE:
            return "CS_BINARY_TYPE";
        case CS_BIT_TYPE:
            return "CS_BIT_TYPE";
        case CS_REAL_TYPE:
            return "CS_REAL_TYPE";
        case CS_FLOAT_TYPE:
            return "CS_FLOAT_TYPE";
        case CS_TEXT_TYPE:
            return "CS_TEXT_TYPE";
        case CS_VARCHAR_TYPE:
            return "CS_VARCHAR_TYPE";
        case CS_VARBINARY_TYPE:
            return "CS_VARBINARY_TYPE";
        case CS_LONGCHAR_TYPE:
            return "CS_LONGCHAR_TYPE";
        case CS_LONGBINARY_TYPE:
            return "CS_LONGBINARY_TYPE";
        case CS_LONG_TYPE:
            return "CS_LONG_TYPE";
        case CS_ILLEGAL_TYPE:
            return "CS_ILLEGAL_TYPE";
        case CS_SENSITIVITY_TYPE:
            return "CS_SENSITIVITY_TYPE";
        case CS_BOUNDARY_TYPE:
            return "CS_BOUNDARY_TYPE";
        case CS_VOID_TYPE:
            return "CS_VOID_TYPE";
        case CS_USHORT_TYPE:
            return "CS_USHORT_TYPE";
        case CS_UNIQUE_TYPE:
            return "CS_UNIQUE_TYPE";
#ifdef CS_UNICHAR_TYPE
        case CS_UNICHAR_TYPE:
            return "CS_UNICHAR_TYPE";
#endif
        default:
            return "(UNKNOWN)";
    }
    /* This point will never be reached */
}
