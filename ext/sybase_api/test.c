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

static CS_RETCODE CS_PUBLIC clientmessage(CS_CONTEXT *context, CS_CONNECTION *connection, CS_CLIENTMSG *message)
{
    fprintf(stderr, "Client message: %d %s\n", message->msgnumber, message->msgstring);
    return CS_SUCCEED;
}

int main(int argc, char **argv)
{
    sybase_link *link= NULL;
    sybase_environment *env= NULL;
    
    if (argc != 5) {
        printf("Usage: %s <host> <username> <password> <sql>\n", argv[0]);
        return 1;
    }
    
    sybase_init(&env);
    sybase_set_messagehandler(env, CS_SERVERMSG_CB, (CS_VOID *)servermessage);
    sybase_set_messagehandler(env, CS_CLIENTMSG_CB, (CS_VOID *)clientmessage);
    
    sybase_alloc(&link);    
    if (sybase_connect(env, link, argv[1], argv[2], argv[3]) == SA_SUCCESS) {
        sybase_result *result= NULL;
        int i = 0;
        
        printf("Connected, executing query...\n");
        if (sybase_query(link, &result, argv[4]) == SA_SUCCESS) {
            while (i++ < 10 && (sybase_results(&result) == SA_SUCCESS)) {
                printf(
                    "result->type %4d [%-20s] result->code %4d [%-20s]\n", 
                    result->type,
                    sybase_nameoftype(result->type), 
                    result->code,
                    sybase_nameofcode(result->code)
                );
            }
        }
        
    } else {
        printf("Connect failed!\n");
    }
    
    sybase_close(link);
    sybase_free(link);
    
    sybase_shutdown(env);
    return 0;
}
