/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"

#define smalloc(s) sybase_mm_malloc((s), __FILE__, __LINE__);
#define srealloc(p, s) sybase_mm_realloc((p), (s), __FILE__, __LINE__);
#define sfree(p) sybase_mm_free((p), __FILE__, __LINE__);

inline void* sybase_mm_malloc(size_t size, char *filename, uint line);
inline void* sybase_mm_realloc(void *ptr, size_t size, char *filename, uint line);
inline void sybase_mm_free(void *ptr, char *filename, uint line);
