#include <stdlib.h>
#include <stdio.h>

/* Regex */
#include <sys/types.h>
#include <regex.h>

#include "csta_connection.h"

int csta_filter(proxy_connection *conn, char *stream, char **retstr) {
	*retstr= NULL;
	
	if (regex_match (stream, "^Monitor start")) {
		*retstr= (char *)malloc (256);
		strncpy (retstr, "-ERR Command not allowed: Monitor start", 256);
		return 1;
	}
	
	return 0;
}
