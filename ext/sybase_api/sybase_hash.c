/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#include <string.h>
#include "sybase_hash.h"
#include "sybase_mm.h"

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
        hash->elements[hash->count].type= HASH_STRING;
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
        hash->elements[hash->count].type= HASH_INT;
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
            case HASH_STRING:
                sfree(hash->elements[i].value.str.val);
                break;
        }
    }
    sfree(hash->elements);
    sfree(hash);
    return SA_SUCCESS;
}

/**
 * Iterative functions: retrieve first element
 * 
 * Usage example:
 * <code>
 *   int c;
 *   sybase_hash_element *e;
 *   for (e= sybase_hash_first(hash, &c); 
 *           sybase_hash_has_more(hash, c); e= sybase_hash_next(hash, &c)) {
 *       ...
 *   }
 * </code>
 *
 * @access  public
 * @param   sybase_hash *hash
 * @param   int *c
 * @return  sybase_hash_element *
 */
SYBASE_API sybase_hash_element *sybase_hash_first(sybase_hash *hash, int *c)
{
    (*c)= 0;
    if (hash->count == 0) {
        return NULL;
    }
    return &hash->elements[0];
}

/**
 * Iterative functions: check whether there are more elements
 * 
 * @access  public
 * @param   sybase_hash *hash
 * @param   int c
 * @return  int
 */
SYBASE_API int sybase_hash_has_more(sybase_hash *hash, int c)
{
    return c < hash->count;
}

/**
 * Iterative functions: retrieve next element
 * 
 * @access  public
 * @param   sybase_hash *hash
 * @param   int *c
 * @return  sybase_hash_element *
 */
SYBASE_API sybase_hash_element *sybase_hash_next(sybase_hash *hash, int *c)
{
    (*c)++;
    if (*c > hash->count) {
        return NULL;
    }
    return &hash->elements[*c];
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
