/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include <sys/types.h>
#include <stdio.h>
#include "sybase_mm.h"
#include <stdlib.h>

void* sybase_mm_malloc(size_t size, char *filename, uint line)
{
    fprintf(stderr, "Allocating %d bytes at %s:%d\n", size, filename, line);
    return malloc(size);
}

void sybase_mm_free(void* ptr, char *filename, uint line)
{
    fprintf(stderr, "Freeing at %s:%d\n", filename, line);
    free(ptr);
}
