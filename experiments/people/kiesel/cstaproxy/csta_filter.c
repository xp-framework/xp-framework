#include <stdlib.h>
#include <stdio.h>
#include <strings.h>

/* Regex, Sockets */
#include <sys/types.h>
#include <sys/socket.h>
#include <regex.h>

#include "cstaproxy.h"
#include "csta_connection.h"

/**
 * Handles connection init, that is when the client has authenticated
 * and the server connection has been created.
 *
 * @access  
 * @param   
 * @return  
 */
int csta_filter_init(proxy_connection *conn) {
	char *response;

	if (NULL == conn->hServer)
		return 0;

	response= (char *)malloc (256);
	snprintf (response, 255, "Monitor start %s\n", conn->phone);
	send (conn->hServer, response, strlen (response), 0);
	free (response);
	
	return 1;
}

/**
 * Handles normal communication
 *
 * @access  
 * @param   
 * @return  
 */
int csta_filter(proxy_connection *conn, char *stream) {
	char *response;
	
	if (regex_match (stream, "^Monitor start")) {
		response= (char *)malloc (256);
		strncpy (response, "-ERR Command not allowed: Monitor start\n", 256);
		send (conn->hClient, response, strlen (response), 0);
		free (response);
		
		return 1;
	}
	
	return 0;
}

/**
 * Handles connection shutdown
 *
 * @access  
 * @param   
 * @return  
 */
int csta_filter_shutdown(proxy_connection *conn) {
	char *response;
			
	/* Check whether the server still is connected */
	if (NULL == conn->hServer)
		return 0;
	
	response= (char*)malloc (256);
	snprintf (response, 255, "Monitor stop %s\n", conn->phone);
	send (conn->hServer, response, strlen (response), 0);
	free (response);
	
	return 1;
}
