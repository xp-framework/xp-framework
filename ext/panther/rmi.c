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

static char rmi_methodidentifiers[] = "GSIE";

int parserequest(const char* str, rmirequest** request)
{
    char buf[33];
    char *tmp;
    int len;
    
    (*request)= malloc(sizeof(rmirequest));

    /* Parse method */
    switch (str[0]) {
        case 'G':
            (*request)->method= RMI_GET;
            break;

        case 'S':
            (*request)->method= RMI_SET;
            break;

        case 'I':
            (*request)->method= RMI_INVOKE;
            break;

        default:
            free((*request));
            return RMI_EMETHOD;
    }
    
    /* Parse length */
    len= strtol(str+ 1, (char**) NULL, 16);
    
    /* Extract class, member and data */
    tmp= malloc(len + 1);
    strlcpy(tmp, str+ 9, len+ 1);
    
    /* Check MD5 */
    MD5Data(tmp, len, buf);
    if (strcmp(buf, str + 1 + 8+ len) != 0) {
        #if 1
        printf(
            "data= (%d)'%s' md5= '%s' cmp= '%s'\n", 
            len,  
            tmp, 
            buf, 
            str + 1 + 8+ len
        );
        #endif
        
        free(tmp);
        free((*request));
        return RMI_ECHECKSUM;
    }

    (*request)->class= strsep(&tmp, ":");
    (*request)->class_len= strlen((*request)->class);
    (*request)->member= strsep(&tmp, ":");
    (*request)->member_len= strlen((*request)->member);
    (*request)->data= tmp;
    (*request)->data_len= strlen((*request)->data);
    (*request)->length= len;
    
    /* Parsing succeeded */
    return RMI_SUCCESS;
}

int responsestring(rmiresponse* response, char** str, int* len)
{
    char buf[33];

    *len= 1 + 8 + response->length + 32;
    *str= (char*)malloc(*len);
    
    /* Set identifier and length */
    snprintf(*str, 10, "%c%-8x", rmi_methodidentifiers[response->method], response->length);
    
    /* Append data */
    strlcat(*str, response->data, 1 + 8 + response->length + 1);
    
    /* Append checksum */
    MD5Data(response->data, response->length, buf);
    strlcat(*str, buf, 1 + 8 + response->length + sizeof(buf) + 1);

    return RMI_SUCCESS;
}
