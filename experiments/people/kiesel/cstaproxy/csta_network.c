#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>

#include "cstaproxy.h"
#include "csta_error.h"

int create_listening_socket (char *addr, int port) {
	int hSocket;
	struct sockaddr_in my_addr;
	int yes;
	
	hSocket = socket(AF_INET, SOCK_STREAM, 0);
	my_addr.sin_family = AF_INET;
	my_addr.sin_port = htons(MYPORT);
	my_addr.sin_addr.s_addr = htonl(INADDR_ANY);
	memset(&(my_addr.sin_zero), '\0', 8);
	
	bind(hSocket, (struct sockaddr *)&my_addr, sizeof(struct sockaddr));
	
	if (setsockopt (hSocket, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof (int)) == -1) {
		ERR("setsockopt failed");
		return -1;
	}
	
	return hSocket;
}
