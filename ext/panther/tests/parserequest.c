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
    rmirequest* request;
    int result;
    
    if (argc != 2) {
        printf(
          "Test request string parsing.\nUsage: %s <string>\n",
          argv[0]
        );
        return 1;
    }
    
    printf(
      "===> (%d)'%s'\n",
      strlen(argv[1]),
      argv[1]
    );
    if ((result= parserequest(argv[1], &request)) == RMI_SUCCESS) {
        int n;
        char* eval;

        printf(
          "---> rmirequest {\n  method= %d\n  length= %d\n  class= (%d)'%s'\n  member= (%d)'%s'\n  data= (%d)'%s'\n}\n",
          request->method,
          request->length,
          request->class_len,
          request->class,
          request->member_len,
          request->member,
          request->data_len,
          request->data
        );
        
        n= sizeof("$registry['']->") + request->class_len + request->member_len;
        eval= (char*) malloc(n);
        strncpy(eval, "$registry['", sizeof("$registry['"));
        strncat(eval, request->class, request->class_len);
        strncat(eval, "']->", sizeof("']->"));
        strncat(eval, request->member, request->member_len);
        
        printf("===> (%d:%d)'%s'\n", n, strlen(eval), eval);

        free(request);
    } else {
        printf("---> Could not parse request, errno= %d\n", result);
    }
    return 0;
}
