/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include <ctpublic.h>

#define SYBASE_API

#define SA_SUCCESS      0
#define SA_FAILURE      1
#define SA_EALLOC       2
#define SA_EALREADYFREE 4
#define SA_ENULLPOINTER 8
#define SA_ECTLIB       16

#define CTLIB_VERSION CS_VERSION_100

typedef struct {
	CS_CONTEXT      *context;
} sybase_environment;

typedef struct {
	CS_CONNECTION   *connection;
} sybase_link;

typedef struct {
	CS_COMMAND *cmd;
	CS_INT type;
	CS_INT code;
} sybase_result;

typedef struct {
	CS_SMALLINT indicator;
	char *value;
	int valuelen;
} sybase_column;

typedef struct {
	int fields;
	CS_DATAFMT *dataformat;
	CS_INT *types;
	sybase_column *columns;
} sybase_resultset;

SYBASE_API int sybase_init(sybase_environment **env);
SYBASE_API int sybase_set_messagehandler(sybase_environment *env, int type, CS_VOID *handler);
SYBASE_API int sybase_shutdown(sybase_environment *env);
SYBASE_API int sybase_alloc(sybase_link **link);
SYBASE_API int sybase_connect(sybase_environment *env, sybase_link *link, char *host, char *user, char *pass);
SYBASE_API CS_INT sybase_connection_status(sybase_link *link);
SYBASE_API int sybase_is_connected(sybase_link *link);
SYBASE_API int sybase_query(sybase_link *link, sybase_result **result, char *query);
SYBASE_API int sybase_cancel(sybase_result *result, int mode);
SYBASE_API int sybase_results(sybase_result **result);
SYBASE_API int sybase_close(sybase_link *link);
SYBASE_API int sybase_free(sybase_link *link);
SYBASE_API char *sybase_nameoftype(CS_INT type);
SYBASE_API char *sybase_nameofcode(CS_INT code);
SYBASE_API char *sybase_nameofdatatype(CS_INT datatype);
SYBASE_API int sybase_free_result(sybase_result *result);
SYBASE_API int sybase_init_resultset(sybase_result *result, sybase_resultset **resultset);
SYBASE_API int sybase_free_resultset(sybase_resultset *resultset);
SYBASE_API int sybase_fetch(sybase_result *result, sybase_resultset **resultset);
