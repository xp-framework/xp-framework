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

/* $Id: zend_engine_2.patch 5217 2005-06-11 23:13:35Z kiesel $ */

#ifndef ZEND_ENUMERATIONS_H
#define ZEND_ENUMERATIONS_H

BEGIN_EXTERN_C()

extern ZEND_API zend_class_entry *base_enumeration_ce;
void zend_register_default_enumeration(TSRMLS_D);

END_EXTERN_C()

#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
