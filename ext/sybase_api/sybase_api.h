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

SYBASE_API int sybase_init(sybase_environment **env);
SYBASE_API int sybase_set_messagehandler(sybase_environment *env, int type, CS_VOID *handler);
SYBASE_API int sybase_shutdown(sybase_environment *env);
SYBASE_API int sybase_alloc(sybase_link **link);
SYBASE_API int sybase_connect(sybase_environment *env, sybase_link *link, char *host, char *user, char *pass);
SYBASE_API int sybase_close(sybase_link *link);
SYBASE_API int sybase_free(sybase_link *link);
