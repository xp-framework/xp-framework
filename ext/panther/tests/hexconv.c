/* This file is part of the XP extension "panter"
 *
 * $Id$ 
 */

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>

int main (int argc, char *argv[])
{
    if (argc != 2) {
        printf(
          "Prints out the decimal representation of a hexadecimal number.\nUsage: %s <hexnum>\n",
          argv[0]
        );
        return 1;
    }
    printf("%ld\n", strtol(argv[1], (char**)NULL, 16));
    return 0;
}
