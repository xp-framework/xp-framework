/* This file is part of the XP extension "sybase_api"
 *
 * $Id$
 */

#ifndef _SYBASE_DEFINES_H
#define _SYBASE_DEFINES_H

#define SYBASE_API

#include <sys/types.h>
#include <stdio.h>
#include <stdlib.h>
#include <ctpublic.h>

#define SA_SUCCESS      0
#define SA_FAILURE      1
#define SA_EALLOC       2
#define SA_EALREADYFREE 4
#define SA_ENULLPOINTER 8
#define SA_ECTLIB       16

#define CTLIB_VERSION CS_VERSION_100

#define HASH_STRING 1
#define HASH_INT    2

#define smalloc(s) sybase_mm_malloc((s), __FILE__, __LINE__);
#define srealloc(p, s) sybase_mm_realloc((p), (s), __FILE__, __LINE__);
#define sfree(p) sybase_mm_free((p), __FILE__, __LINE__);

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

typedef struct {
    CS_CONTEXT      *context;
} sybase_environment;

typedef struct {
    CS_CONNECTION   *connection;
} sybase_link;

typedef struct {
    CS_COMMAND *cmd;
    CS_INT type;
    CS_INT code;
} sybase_result;

typedef struct {
    CS_SMALLINT indicator;
    char *value;
    int valuelen;
} sybase_column;

typedef struct {
    int fields;
    CS_DATAFMT *dataformat;
    CS_INT *types;
    sybase_column *columns;
} sybase_resultset;

#endif
