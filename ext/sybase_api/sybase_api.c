/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"
#include "sybase_mm.h"

/**
 * Setup environment
 * 
 * @param   sybase_environment **env
 * @return  int
 */
SYBASE_API int sybase_init(sybase_environment **env)
{   
    sybase_environment *e;
    
    e= (sybase_environment *) smalloc(sizeof(sybase_environment));    
	if (cs_ctx_alloc(CTLIB_VERSION, &e->context) != CS_SUCCEED || 
        ct_init(e->context, CTLIB_VERSION) != CS_SUCCEED) {
        sfree(e);
		return SA_FAILURE | SA_EALLOC;
	}
    
    *env= e;
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
 * 2) Client message handler:
 *    =======================
 *    static CS_RETCODE CS_PUBLIC clientmessage(
 *      CS_CONTEXT *context, 
 *      CS_CONNECTION *connection, 
 *      CS_CLIENTMSG *message
 *    )
 * </pre>
 *
 * @param   sybase_environment *env
 * @param   int type
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
 * @param   sybase_environment *env the environment previously initialized with sybase_init
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_connect(sybase_environment *env, sybase_link *link, char *host, char *user, char *pass)
{
    if (ct_con_alloc(env->context, &link->connection) != CS_SUCCEED) {
        return SA_FAILURE | SA_EALLOC;
    }

	if (user) {
		ct_con_props(link->connection, CS_SET, CS_USERNAME, user, CS_NULLTERM, NULL);
	}
	if (pass) {
		ct_con_props(link->connection, CS_SET, CS_PASSWORD, pass, CS_NULLTERM, NULL);
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
 * @param   sybase_link *link
 * @return  CS_INT a bitfield of CS_CONSTAT_CONNECTED and CS_CONSTAT_DEAD
 */
SYBASE_API CS_INT sybase_connection_status(sybase_link *link)
{
    CS_INT status;
    
    if (ct_con_props(link->connection, CS_GET, CS_CON_STATUS, &status, CS_UNUSED, NULL) != CS_SUCCEED) {
        status = CS_CONSTAT_DEAD;
    }
    
    return status;
}

/**
 * Check if whe are connected
 * 
 * @param   sybase_link *link
 * @return  int
 */
SYBASE_API int sybase_is_connected(sybase_link *link)
{
    return (sybase_connection_status(link) & CS_CONSTAT_CONNECTED) ? SA_SUCCESS : SA_FAILURE;
}

/**
 * Close the connection to the database
 * 
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
