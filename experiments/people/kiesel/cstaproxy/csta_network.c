#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/time.h>
#include <netinet/in.h>


#include "cstaproxy.h"
#include "csta_error.h"
#include "csta_network.h"
#include "csta_connection.h"

int create_listening_socket (char *addr, int port) {
	int hSocket;
	struct sockaddr_in my_addr;
	int yes;
	
	hSocket = socket(AF_INET, SOCK_STREAM, 0);
	my_addr.sin_family = AF_INET;
	my_addr.sin_port = htons(MYPORT);
	my_addr.sin_addr.s_addr = htonl(INADDR_ANY);
	memset(&(my_addr.sin_zero), '\0', 8);
	
	if (-1 == setsockopt (hSocket, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof (int))) {
		ERR("setsockopt failed");
		return -1;
	}
	
	if (-1 == bind(hSocket, (struct sockaddr *)&my_addr, sizeof(struct sockaddr))) {
		ERR("bind failed");
		return -1;
	}
	
	if (-1 == listen (hSocket, 10)) {
		ERR("listen failed");
		return -1;
	}
	
	LOG("Listener created.");
	return hSocket;
}

int create_proxy_connection(fd_set *master, int hListen) {
	struct sockaddr_in remoteaddr;
	/* proxy_connection *conn; */
	int addrlen;
	int newfd;
	
	addrlen= sizeof (remoteaddr);
	if (-1 == (newfd= accept(hListen, (struct sockaddr *)&remoteaddr, &addrlen))) {
		ERR("Could not accept new connection");
		return -1;
	}
	
	/* alloc_connection(&conn);
	conn->hClient= newfd;*/

	/* Add socket to fdset */
	FD_SET (newfd, master);
	
	LOG("New client connected");
	return newfd;
}

int process_client(fd_set *master, proxy_connection *pc, int hSocket) {
	char buf[256];
	int nbytes;
	
	memset (&buf, '\0', sizeof (buf));
	
	/* Handle data from client */
	if (0 >= (nbytes= recv (hSocket, buf, sizeof (buf), 0))) {
		if (0 == nbytes) {
			log (__FILE__, __LINE__, "Client %d closed connection.", hSocket);
		} else {
			ERR ("Error reading from socket");
		}
		
		close (hSocket);
		FD_CLR(hSocket, master);
	} else {
		printf ("Client sends: [%s]\n", buf);
	}
	
	return 1;
}

int select_loop(int hListen) {
	fd_set	master, read_fs;
	int fdMax, fdNew, i;
	int quit;
	proxy_connection *conn, *pc;
	
	/* Add listener to sockets and remember max socket */
	FD_SET (hListen, &master);
	fdMax= hListen;
	
	/* Initialize array of connections */
	conn= (proxy_connection *)malloc(10 * sizeof (proxy_connection *));
	for (i= 0; i < sizeof (conn); i++)
		init_connection (&conn[i]);
	
	quit= 0;
	while (!quit) {
		/* Make a copy */
		read_fs= master;
		
		if (-1 == select(fdMax+1, &read_fs, NULL, NULL, NULL)) {
			ERR("Error in select()");
			return -1;
		}
		
		for (i= 0; i <= fdMax; i++) {
			/* Check for active connections... */
			if (FD_ISSET (i, &read_fs)) {
				if (i == hListen) {
					/* New connection coming in */
					if (-1 != (fdNew= create_proxy_connection(&master, i))) {
						if (fdNew > fdMax) fdMax= fdNew;
					}
				} else {
					/* This is a normal client connection */
					
					/* Retrieve the proxy_connection for this socket */
					pc= (proxy_connection *)get_connection_by_socket (conn, i);
					
					process_client(&master, pc, i);
				}
			}
		}
	}
	
	return 1;
}
