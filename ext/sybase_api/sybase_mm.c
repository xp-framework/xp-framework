/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_mm.h"

inline void* sybase_mm_malloc(size_t size, char *filename, uint line)
{
    #ifdef SYBASE_MM_DEBUG
    fprintf(stderr, "Allocating %d bytes at %s:%d\n", size, filename, line);
    #endif
    return malloc(size);
}

inline void* sybase_mm_realloc(void *ptr, size_t size, char *filename, uint line)
{
    #ifdef SYBASE_MM_DEBUG
    fprintf(stderr, "Reallocating %d bytes at %s:%d\n", size, filename, line);
    #endif
    return realloc(ptr, size);
}

inline void sybase_mm_free(void* ptr, char *filename, uint line)
{
    #ifdef SYBASE_MM_DEBUG
    fprintf(stderr, "Freeing at %s:%d\n", filename, line);
    #endif
    free(ptr);
}
