/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#define smalloc(s) sybase_mm_malloc(s, __FILE__, __LINE__);
#define sfree(s) sybase_mm_free(s, __FILE__, __LINE__);

void* sybase_mm_malloc(size_t size, char *filename, uint line);
void sybase_mm_free(void* ptr, char *filename, uint line);
