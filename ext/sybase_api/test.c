/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"
#include "sybase_mm.h"
#include "sybase_hash.h"
#include <readline/readline.h>
#include <readline/history.h>

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

#define HLINE "     ---------------------------------------------------------------------------------\n"

int main(int argc, char **argv)
{
    sybase_link *link= NULL;
    sybase_hash *properties= NULL;
    sybase_environment *env= NULL;
    
    if (argc != 4) {
        printf("Usage: %s <host> <username> <password>\n", argv[0]);
        return 1;
    }
    
    sybase_init(&env);
    sybase_set_messagehandler(env, CS_SERVERMSG_CB, (CS_VOID *)servermessage);
    sybase_set_messagehandler(env, CS_CLIENTMSG_CB, (CS_VOID *)clientmessage);
    
    sybase_alloc(&link);
    sybase_hash_init(&properties, 10, 10);
    sybase_hash_addstring(properties, CS_APPNAME, argv[0]);
    sybase_hash_addstring(properties, CS_HOSTNAME, "FreeBSD");
    
    if (sybase_connect(env, link, argv[1], argv[2], argv[3], properties) == SA_SUCCESS) {
        sybase_result *result= NULL;
        sybase_resultset *resultset= NULL;
        char *sql, *prompt;
        
        printf("===> Connected\n");

        prompt= (char*) malloc(strlen(argv[2])+ 1+ strlen(argv[1])+ 3);
        sprintf(prompt, "%s@%s > ", argv[2], argv[1]);
        while (1) {
            sql= readline(prompt);
            if (strncmp(sql, "quit", 4) == 0) {
                free(sql);
                break;
            }
            add_history(sql);
            
            printf("---> Executing query '%s':\n", sql);
            if (sybase_query(link, &result, sql) == SA_SUCCESS) {
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

                            /* Print out field information */
                            printf(HLINE);
                            for (i= 0; i < resultset->fields; i++) {
                                printf(
                                    "     field #%d: datatype %3d [%-20s] name '%s'\n",
                                    i,
                                    resultset->types[i],
                                    sybase_nameofdatatype(resultset->types[i]),
                                    resultset->dataformat[i].name
                                );
                            }
                            printf(HLINE);

                            /* Print out field contents */
                            while (sybase_fetch(result, &resultset) == SA_SUCCESS) {
                                for (i= 0; i < resultset->fields; i++) {
                                    printf(
                                        "     %-32s: [%d:%d] '%s'\n", 
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

                        case CS_CMD_SUCCEED:
                            printf("---> Affected rows: %d\n", sybase_rowcount(result));
                            break;

                        case CS_CMD_FAIL:
                        case CS_CANCELED:
                            done= 1;
                            break;
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
            free(sql);
        }
    } else {
        printf("---> Connect failed!\n");
    }
    
    sybase_close(link);
    sybase_free(link);
    
    sybase_hash_free(properties);
    
    sybase_shutdown(env);
    
    printf("===> Done\n");
    return 0;
}
