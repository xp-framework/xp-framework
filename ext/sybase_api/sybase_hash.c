/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include "sybase_api.h"
#include "sybase_mm.h"
#include "sybase_hash.h"

/**
 * Initialize a hash
 * 
 * @access  public
 * @param   sybase_hash **hash
 * @param   int initial
 * @param   int grow
 * @return  int
 */
SYBASE_API int sybase_hash_init(sybase_hash **hash, int initial, int grow)
{   
    *hash= (sybase_hash *) smalloc(sizeof(sybase_hash));
    if (!*hash) {
        return SA_FAILURE | SA_EALLOC;
    }
    (*hash)->count= 0;
    (*hash)->grow= grow;
    
    /* Allocate initial amount */
    (*hash)->elements= (sybase_hash_element *) smalloc(sizeof(sybase_hash_element *) * initial);
    (*hash)->allocated= initial;
    
    return SA_SUCCESS;
}

/**
 * Check hashmap allocation and grow by the defined number of blocks
 * if necessary
 * 
 * @access  private
 * @param   sybase_hash **hash
 * @return  int
 */
static inline int _check_allocation(sybase_hash *hash)
{
    if (hash->count >= hash->allocated) {
        hash->elements= (sybase_hash_element *) realloc(
            hash->elements, 
            sizeof(sybase_hash_element) * (hash->allocated + hash->grow)
        );
        if (!hash->elements) {
            return SA_FAILURE | SA_EALLOC;
        }
        hash->allocated+= hash->grow;
    }
    return SA_SUCCESS;
}

/**
 * Add a string to the hash
 * 
 * @access  public
 * @param   sybase_hash **hash
 * @param   int key
 * @param   char *value
 * @return  int
 */
SYBASE_API int sybase_hash_addstring(sybase_hash *hash, int key, char *value)
{   
    int retcode;

    if ((retcode= _check_allocation(hash)) == SA_SUCCESS) {
        hash->elements[hash->count].key= key;
        hash->elements[hash->count].type= CS_NULLTERM;
        hash->elements[hash->count].value.str.val= (char *) strdup(value);
        hash->elements[hash->count].value.str.len= strlen(value);
        hash->count++;
    }
    return retcode;
}

/**
 * Add an integer to the hash
 * 
 * @access  public
 * @param   sybase_hash **hash
 * @param   int key
 * @param   int value
 * @return  int
 */
SYBASE_API int sybase_hash_addint(sybase_hash *hash, int key, int value)
{
    int retcode;

    if ((retcode= _check_allocation(hash)) == SA_SUCCESS) {
        hash->elements[hash->count].key= key;
        hash->elements[hash->count].type= CS_UNUSED;
        hash->elements[hash->count].value.lval= value;
        hash->count++;
    }
    return retcode;
}

/**
 * Free a hash
 * 
 * @access  public
 * @param   sybase_hash *hash
 * @return  int
 */
SYBASE_API int sybase_hash_free(sybase_hash *hash)
{
    int i;

    if (!hash) {
        return SA_FAILURE | SA_EALREADYFREE;
    }
    
    for (i= 0; i < hash->count; i++) {
        switch ((int)hash->elements[i].type) {
            case CS_NULLTERM:
                sfree(hash->elements[i].value.str.val);
                break;
        }
    }
    sfree(hash->elements);
    sfree(hash);
    return SA_SUCCESS;
}

/**
 * Apply a function to the hash
 * 
 * @access  public
 * @param   sybase_hash *hash
 * @param   sybase_hash_apply_func_t func
 * @return  int
 */
SYBASE_API int sybase_hash_apply(sybase_hash *hash, sybase_hash_apply_func_t func)
{
    int i;
    
    for (i= 0; i < hash->count; i++) {
        func(&hash->elements[i]);
    }
    return SA_SUCCESS;
}
