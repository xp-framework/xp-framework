/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) 1998-2004 Zend Technologies Ltd. (http://www.zend.com) |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        | 
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Alex Kiesel <alex@kiesel.name>                              |
   +----------------------------------------------------------------------+
*/

/* $Id$ */

#include "zend.h"
#include "zend_API.h"
#include "zend_reflection_api.h"
#include "zend_builtin_functions.h"
#include "zend_interfaces.h"
#include "zend_exceptions.h"
#include "zend_enumerations.h"

ZEND_API zend_class_entry *base_enumeration_ce;

ZEND_METHOD(enumeration, __clone)
{

	/* Should never be executable */
	zend_throw_exception(NULL, "Cannot clone object using __clone()", 0 TSRMLS_CC);
}

ZEND_METHOD(enumeration, __construct)
{

	/* Should never be executable */
	zend_throw_exception(NULL, "Cannot clone object using __clone()", 0 TSRMLS_CC);
}

ZEND_METHOD(enumeration, size)
{
	RETURN_LONG(zend_hash_num_elements(&EG(scope)->constants_table));
}

ZEND_METHOD(enumeration, values)
{
	HashPosition pos;
	zval **ordinal, *container;
	char *name;
	uint length;
	ulong dummy;
	
	array_init(return_value);
	
	/* Go through class constants and add values to the return array */
	zend_hash_internal_pointer_reset_ex(&EG(scope)->constants_table, &pos);
	while (zend_hash_get_current_data_ex(&EG(scope)->constants_table, (void **)&ordinal, &pos) == SUCCESS) {
		zend_hash_get_current_key_ex(&EG(scope)->constants_table, &name, &length, &dummy, 0, &pos);

		/* Create object for this enumeration memeber */
		MAKE_STD_ZVAL(container);
		if (object_init_ex(container, EG(scope)) != SUCCESS) {
			zend_error(E_CORE_ERROR, "Cannot create object in Enumeration::values()");
		}
		add_property_stringl(container, "name", name, length, 1);
		add_property_long(container, "ordinal", (*ordinal)->value.lval);

		/* Add to result array and proceed to next element */
		zend_hash_next_index_insert(Z_ARRVAL_P(return_value), &container, sizeof(zval *), NULL);
		zend_hash_move_forward_ex(&EG(scope)->constants_table, &pos);
	}
}

ZEND_METHOD(enumeration, valueOf) {
	long needle;
	char *name;
	uint length;
	ulong dummy;
	HashPosition pos;
	zval **ordinal;
	
	if (ZEND_NUM_ARGS() != 1)
		WRONG_PARAM_COUNT;
	
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &needle) == FAILURE) {
		zend_error(E_WARNING, "valueOf() expects integer.");
		return;
	}
	
	/* Iterate over constants until the searched needle is found */
	zend_hash_internal_pointer_reset_ex(&EG(scope)->constants_table, &pos);
	while (zend_hash_get_current_data_ex(&EG(scope)->constants_table, (void **)&ordinal, &pos) == SUCCESS) {
	
		/* Compare current value with searched one. If they equal, it's the right one, because enums are unique */
		if ((*ordinal)->value.lval == needle) {
			zend_hash_get_current_key_ex(&EG(scope)->constants_table, &name, &length, &dummy, 0, &pos);
			
			object_init_ex(return_value, EG(scope));
			add_property_stringl(return_value, "name", name, length, 1);
			add_property_long(return_value, "ordinal", (*ordinal)->value.lval);
			return;
		}
		
		zend_hash_move_forward_ex(&EG(scope)->constants_table, &pos);
	}
	
	RETURN_FALSE;
}

static zend_function_entry default_enumeration_functions[]= {
	ZEND_ME(enumeration, __clone, NULL, ZEND_ACC_PRIVATE|ZEND_ACC_FINAL)
	ZEND_ME(enumeration, __construct, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_FINAL)
	ZEND_ME(enumeration, size, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_FINAL)
	ZEND_ME(enumeration, values, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_FINAL)
	ZEND_ME(enumeration, valueOf, NULL, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC|ZEND_ACC_FINAL)
};

void zend_register_default_enumeration(TSRMLS_D)
{
	zend_class_entry ce;
	INIT_CLASS_ENTRY(ce, "Enumeration", default_enumeration_functions);
	base_enumeration_ce = zend_register_internal_class(&ce TSRMLS_CC);
}
