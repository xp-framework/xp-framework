/* This program is part of the XP framework
 *
 * $Id$
 */

#include <stdlib.h>
#include <stdio.h>		/* for printf */
#include <stdarg.h>		/* va_* */

/* Socket */
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

/* Regex */
#include <regex.h>

#include "cstaproxy.h"
#include "csta_error.h"
#include "csta_network.h"

/**
 * Performs a regular expression match.
 *
 * @access  public
 * @param   char *haystack
 * @param	char *needle
 * @return  bool matches
 */
int regex_match (char *haystack, char *needle) {
	regex_t *rctx;
	regmatch_t *pmatch[2];
	int result, retcode;
	
	result= FALSE;
	rctx= (regex_t *)malloc (sizeof (regex_t));
	
	/* Compile the regular expression */
	if (0 != (retcode= regcomp (rctx, needle, REG_EXTENDED|REG_ICASE|REG_NEWLINE))) {
		char *errbuf;
		
		errbuf= malloc (sizeof (char)*256);
		regerror (retcode, rctx, errbuf, 256);
		print_error (__FILE__, __LINE__, "%s", errbuf);
		free (errbuf);
		regfree (rctx);
		
		return FALSE;
	}
	
	/* Allocate memory for matches */
	*pmatch= (regmatch_t*)malloc (sizeof (regmatch_t) * 10);
	
	/* Execute regular expression */
	if (0 != (retcode= regexec (rctx, haystack, sizeof (pmatch), *pmatch, 0))) {
		return FALSE;
	}
	
	return TRUE;
}

/**
 * The main program
 *
 * @access  public
 * @param   int argc
 * @param   int argv
 * @return  int 
 */
int main(int argc, char **argv) {
	int hListen;
	
	/* Create listener and add it to the sockets set */
	if (-1 == (hListen= create_listening_socket (MYADDR, MYPORT))) {
		ERR("Could not create listening socket");
		exit (1);
	}

	select_loop (hListen);	
	
	return 0;
}
