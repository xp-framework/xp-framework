/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

#include <setjmp.h>
#include "exceptions.h"

int main(int argc, char** argv) 
{
    try {
        if (argc > 1) {
            throw (new Exception(argv[1]));
        }
        printf("Running\n");
    } catch (Exception e) {
        printf("Caught exception %s\n", e.message);
        return 1;
    }
    return 0;
}
