#include <stdlib.h>
#include <stdio.h>

#include "cstaproxy.h"
#include "csta_error.h"
#include "csta_connection.h"
#include "auth_sybmetha.h"
#include "sybase_api/sybase_mm.h"
#include "sybase_api/sybase_hash.h"
#include "sybase_api/sybase_api.h"

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

int authenticate(proxy_connection *conn, char *username, char *password) {
	sybase_link *link= NULL;
	sybase_environment *env= NULL;
	sybase_result *result= NULL;
	sybase_resultset *resultset= NULL;
	sybase_hash *properties= NULL;
	int done= 0, count= 0, i= 0;
	char *query= NULL, *phone= NULL, *realuser= NULL;
	
	sybase_init (&env);
	sybase_set_messagehandler(env, CS_SERVERMSG_CB, (CS_VOID *)servermessage);
	sybase_set_messagehandler(env, CS_CLIENTMSG_CB, (CS_VOID *)clientmessage);
	
	sybase_alloc (&link);
	
	sybase_hash_init(&properties, 10, 10);
	sybase_hash_addstring(properties, CS_APPNAME, "cstaproxy");
	sybase_hash_addstring(properties, CS_HOSTNAME, "FreeBSD");
	
	if (SA_SUCCESS != sybase_connect (env, link, METHA_SERVER, METHA_USERNAME, METHA_PASSWD, properties)) {
		ERR("Connection to sybase failed!");
		
		sybase_close (link);
		sybase_free (link);
		sybase_shutdown (env);
		
		return 0;
	}
	
	query= (char*)malloc (1024);
	snprintf (
		query, 
		1024, 
		"select username, phone from person where username= '%s' and bz_id= 20000 and password= '%s'",
		username,
		password
	);
	
	if (SA_SUCCESS == sybase_query (link, &result, query)) {
		while (!done && sybase_results(&result) == SA_SUCCESS) {
			switch ((int)result->type) {
				case CS_ROW_RESULT:
					sybase_init_resultset (result, &resultset);
					
					while (sybase_fetch (result, &resultset) == SA_SUCCESS) {
						for (i= 0; i < resultset->fields; i++) {
							count++;
							if (0 == strncmp (resultset->dataformat[i].name, "phone", 7)) {
								phone= (char*)malloc (strlen (resultset->columns[i].value)+1);
								strncpy (phone, resultset->columns[i].value, strlen (resultset->columns[i].value));
							}
							if (0 == strncmp (resultset->dataformat[i].name, "username", 10)) {
								realuser= (char*)malloc (strlen (resultset->columns[i].value)+1);
								strncpy (realuser, resultset->columns[i].value, strlen (resultset->columns[i].value));
							}
						}
					}
					
					sybase_free_resultset (resultset);

				case CS_CMD_FAIL:
				case CS_CANCELED:
					done= 1;
			}
		}
	}
	
	if (NULL != phone) { pc_set_phone(conn, phone); }
	if (NULL != realuser) { pc_set_username(conn, realuser); }

	/* Now do the actual auth */
	if (NULL != realuser) {
		conn->is_authenticated= 1;
	}

	free (phone);
	free (realuser);
	free (query);
	
	sybase_close (link);
	sybase_free (link);
	sybase_shutdown (env);
	
	return count;		
}
