#include <stdlib.h>
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
}

proxy_connection *get_connection_by_socket(proxy_connection *conn, int hSocket) {
	int i;
	
	for (i= 0; i < sizeof (conn); i++) {
		if (hSocket == conn[i].hClient || hSocket == conn[i].hServer)
			return (proxy_connection *)&conn[i];
	}
	
	ERR("Could not find socket by socket id");
	return NULL;
}

int add_connection(proxy_connection *conn, int cnt, proxy_connection *add) {
	
}
