/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#ifndef _SYBASE_API_H
#define _SYBASE_API_H

#include "sybase_defines.h"

SYBASE_API int sybase_init(sybase_environment **env);
SYBASE_API int sybase_set_messagehandler(sybase_environment *env, int type, CS_VOID *handler);
SYBASE_API int sybase_shutdown(sybase_environment *env);
SYBASE_API int sybase_alloc(sybase_link **link);
SYBASE_API int sybase_connect(sybase_environment *env, sybase_link *link, char *host, char *user, char *pass, sybase_hash *props);SYBASE_API CS_INT sybase_connection_status(sybase_link *link);
SYBASE_API int sybase_is_connected(sybase_link *link);
SYBASE_API int sybase_query(sybase_link *link, sybase_result **result, char *query);
SYBASE_API int sybase_cancel(sybase_result *result, int mode);
SYBASE_API int sybase_results(sybase_result **result);
SYBASE_API int sybase_close(sybase_link *link);
SYBASE_API int sybase_free(sybase_link *link);
SYBASE_API char *sybase_nameoftype(CS_INT type);
SYBASE_API char *sybase_nameofcode(CS_INT code);
SYBASE_API char *sybase_nameofdatatype(CS_INT datatype);
SYBASE_API int sybase_rowcount(sybase_result *result);
SYBASE_API int sybase_free_result(sybase_result *result);
SYBASE_API int sybase_init_resultset(sybase_result *result, sybase_resultset **resultset);
SYBASE_API int sybase_free_resultset(sybase_resultset *resultset);
SYBASE_API int sybase_fetch(sybase_result *result, sybase_resultset **resultset);
#endif
