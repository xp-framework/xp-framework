/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"

typedef struct {
    int key;
    CS_INT type;
    union {
        struct {
            char* val;
            int len;
        } str;
        int lval;
    } value;
} sybase_hash_element;

typedef struct {
    int count;
    int grow;
    int allocated;
    sybase_hash_element *elements;
} sybase_hash;

typedef void (*sybase_hash_apply_func_t)(sybase_hash_element *e);

SYBASE_API int sybase_hash_init(sybase_hash **hash, int initial, int grow);
SYBASE_API int sybase_hash_addstring(sybase_hash *hash, int key, char *value);
SYBASE_API int sybase_hash_addint(sybase_hash *hash, int key, int value);
SYBASE_API int sybase_hash_free(sybase_hash *hash);
SYBASE_API int sybase_hash_apply(sybase_hash *hash, sybase_hash_apply_func_t func);
