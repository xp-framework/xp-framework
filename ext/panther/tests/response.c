/* This file is part of the XP extension "panter"
 *
 * $Id$ 
 */

#include <sys/types.h>
#include <md5.h>
#include <stdio.h>
#include <stdlib.h>
#include <limits.h>
#include <string.h>
#include "rmi.h"

int main (int argc, char *argv[])
{
    rmirequest* response;
    char* str;
    int len;
    
    if (argc != 3) {
        printf(
          "Test response string.\nUsage: %s <method> <data>\n",
          argv[0]
        );
        return 1;
    }
    
    response= malloc(sizeof(rmiresponse));
    response->method= atoi(argv[1]);
    response->length= strlen(argv[2]);
    response->data= (char*) malloc(response->length+ 1);
    strlcpy(response->data, argv[2], response->length+ 1);

    printf(
        "---> rmiresponse {\n  method= %d\n  length= %d\n  data= (%d)'%s'\n}\n",
        response->method,
        response->length,
        strlen(response->data),
        response->data
    );
    
    responsestring(response, (char**)&str, &len);
    
    printf("===> (%d)'%s'\n", len, str);
    
    return 0;
}
