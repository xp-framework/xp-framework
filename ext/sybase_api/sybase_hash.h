/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#ifndef _SYBASE_HASH_H
#define _SYBASE_HASH_H

#include "sybase_defines.h"

SYBASE_API int sybase_hash_init(sybase_hash **hash, int initial, int grow);
SYBASE_API int sybase_hash_addstring(sybase_hash *hash, int key, char *value);
SYBASE_API int sybase_hash_addint(sybase_hash *hash, int key, int value);
SYBASE_API int sybase_hash_free(sybase_hash *hash);
SYBASE_API sybase_hash_element *sybase_hash_first(sybase_hash *h, int *c);
SYBASE_API int sybase_hash_has_more(sybase_hash *h, int c);
SYBASE_API sybase_hash_element *sybase_hash_next(sybase_hash *h, int *c);
SYBASE_API int sybase_hash_apply(sybase_hash *hash, sybase_hash_apply_func_t func);
#endif
