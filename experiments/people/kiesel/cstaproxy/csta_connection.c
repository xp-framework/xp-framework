/* This program is part of the XP framework
 *
 * $Id$
 */

#include <stdlib.h>
#include <stdio.h>
#include "csta_error.h"
#include "csta_connection.h"

int alloc_connection(proxy_connection **conn) {
	*conn= (proxy_connection *)malloc (sizeof (proxy_connection));
	
	if (!*conn) {
		ERR("Could not malloc() enough memory");
		return 0;
	}
	
	init_connection (*conn);
	return 1;
}

void init_connection(proxy_connection *conn) {
	conn->hClient= NULL;
	conn->hServer= NULL;
	conn->is_authenticated= 0;
	conn->username= NULL;
}

void free_connection(proxy_connection **conn) {
	free (*conn);
	*conn= NULL;
}

void alloc_connection_context(connection_context **ctx) {
	int i;
	
	*ctx= (connection_context *)malloc (sizeof (connection_context));
	if(!*ctx) {
		ERR("Could not malloc() enough memory");
		return 0;
	}
	
	/* By default allocate space for 10 connections */
	(*ctx)->count= 0;
	(*ctx)->allocated= 10;
	(*ctx)->connections= (proxy_connection **)malloc(10 * sizeof (proxy_connection));
	
	/* Set them all to NULL */
	for (i= 0; i < 10; i++) {
		(*ctx)->connections[i]= NULL;
	}
}

proxy_connection *get_connection_by_socket(connection_context *ctx, int hSocket) {
	int i;
	
	/* Return proxy_connection for a socket when the socket it either client or server */
	for (i= 0; i < ctx->count; i++) {
		if (hSocket == ctx->connections[i]->hClient || 
			hSocket == ctx->connections[i]->hServer)
			return ctx->connections[i];
	}
	
	ERR("Could not find socket by socket id");
	return NULL;
}

int add_connection(connection_context *ctx, proxy_connection *add) {
	/* Check if enough memory is allocated */
	if (ctx->count >= ctx->allocated) {
		ctx->connections= (proxy_connection **)realloc (ctx->connections, (ctx->count+1) * sizeof (proxy_connection));
		ctx->allocated++;
	}
	
	/* Add the new connection */
	ctx->connections[ctx->count]= add;
	ctx->count++;
	
	log (__FILE__, __LINE__, "New context connection: client #%d", add->hClient);
	return 1;
}

int delete_connection(connection_context *ctx, proxy_connection *del) {
	int i;
	
	for (i= 0; i < ctx->count; i++) {
		if (ctx->connections[i] == del) {
			/* Move the connection from the end to the newly empty position */
			ctx->connections[i]= ctx->connections[ctx->count-1];
			ctx->connections[ctx->count-1]= NULL;
			ctx->count--;
			
			return 1;
		}
	}
	
	ERR("Unable to remove connection");
	return 0;
}

void _dump_context(connection_context *ctx) {
	int i;
	
	printf ("===> Dump of connection context:\n");
	printf ("  # of connections: %d\n", ctx->count);
	printf ("  # of allocations: %d\n", ctx->allocated);
	
	for (i= 0; i < ctx->allocated; i++) {
		printf ("  Conn #%d at 0x%x\n", i, (int)ctx->connections[i]);
	}
}
