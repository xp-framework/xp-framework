/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#ifndef _SYBASE_MM_H
#define _SYBASE_MM_H

#include "sybase_defines.h"

inline void* sybase_mm_malloc(size_t size, char *filename, uint line);
inline void* sybase_mm_realloc(void *ptr, size_t size, char *filename, uint line);
inline void sybase_mm_free(void *ptr, char *filename, uint line);

#endif
