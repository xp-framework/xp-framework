/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"

static CS_RETCODE CS_PUBLIC servermessage(CS_CONTEXT *context, CS_CONNECTION *connection, CS_SERVERMSG *message)
{
    fprintf(
        stderr, 
        "***  Server %s message #%d, severity %d at %s line %d state %d status %d\n"
        "     %s\n",
        message->svrname,
        message->msgnumber, 
        message->severity, 
        message->proc,
        message->line,
        message->state,
        message->status,
        message->text
    );
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
        sybase_resultset *resultset= NULL;
        
        printf("===> Connected to %s@%s\n", argv[2], argv[1]);
        printf("---> Executing query '%s':\n", argv[4]);
        if (sybase_query(link, &result, argv[4]) == SA_SUCCESS) {
            int done= 0;
            int i= 0;

            while (!done && (sybase_results(&result) == SA_SUCCESS)) {
                printf(
                    "     result->type %4d [%-20s] result->code %4d [%-20s]\n", 
                    result->type,
                    sybase_nameoftype(result->type), 
                    result->code,
                    sybase_nameofcode(result->code)
                );
                switch ((int)result->type) {
                    case CS_ROW_RESULT:
                        sybase_init_resultset(result, &resultset);
                        for (i= 0; i < resultset->fields; i++) {
                            printf(
                                "     field #%d: datatype %3d [%-20s] name '%s'\n",
                                i,
                                resultset->types[i],
                                sybase_nameofdatatype(resultset->types[i]),
                                resultset->dataformat[i].name
                            );
                        }
                        while (sybase_fetch(result, &resultset) == SA_SUCCESS) {
                            for (i= 0; i < resultset->fields; i++) {
                                printf(
                                    "     %-19s: [%d:%d] '%s'\n", 
                                    resultset->dataformat[i].name, 
                                    resultset->columns[i].indicator,
                                    resultset->columns[i].valuelen,
                                    resultset->columns[i].value
                                );
                            }
                            printf("\n");
                        }
                        sybase_free_resultset(resultset);
                        break;

                    case CS_CMD_FAIL:
                    case CS_CANCELED:
                        done= 1;
                }
            }
            printf(
                "---> result->type %4d [%-20s] result->code %4d [%-20s]\n", 
                result->type,
                sybase_nameoftype(result->type), 
                result->code,
                sybase_nameofcode(result->code)
            );
            sybase_free_result(result);
        }
        
    } else {
        printf("---> Connect failed!\n");
    }
    
    sybase_close(link);
    sybase_free(link);
    
    sybase_shutdown(env);
    
    printf("===> Done\n");
    return 0;
}
