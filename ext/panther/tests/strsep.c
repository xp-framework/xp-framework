/* This file is part of the XP extension "panter"
 *
 * $Id$ 
 */

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>
#include <string.h>

int main (int argc, char *argv[])
{
    char* c;
    
    if (argc != 3) {
        printf(
          "String seperation test.\nUsage: %s <string> <delim>\n",
          argv[0]
        );
        return 1;
    }
    
    while ((c= strsep(&argv[1], argv[2]))) {
        printf("'%s'\n", c);
    }
    return 0;
}
