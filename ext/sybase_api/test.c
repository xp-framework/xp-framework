/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"

static CS_RETCODE CS_PUBLIC servermessage(CS_CONTEXT *context, CS_CONNECTION *connection, CS_SERVERMSG *message)
{
    fprintf(stderr, "Server message: %d %s\n", message->msgnumber, message->text);
    return CS_SUCCEED;
}

int main(int argc, char **argv)
{
    sybase_link *link= NULL;
    sybase_environment *env= NULL;
    
    if (argc != 4) {
        printf("Usage: %s <host> <username> <password>\n", argv[0]);
        return 1;
    }
    
    sybase_init(&env);
    sybase_set_messagehandler(env, CS_SERVERMSG_CB, (CS_VOID *)servermessage);
    
    sybase_alloc(&link);    
    if (sybase_connect(env, link, argv[1], argv[2], argv[3]) == SA_SUCCESS) {
        printf("Connected:)\n");
    } else {
        printf("Connect failed!\n");
    }
        
    sybase_close(link);
    sybase_free(link);
    
    sybase_shutdown(env);
    return 0;
}
