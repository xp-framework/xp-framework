/* A Bison parser, made from /mnt/home/alex/cvs/php/Zend/zend_ini_parser.y, by GNU bison 1.75.  */

/* Skeleton parser for Yacc-like parsing with Bison,
   Copyright (C) 1984, 1989, 1990, 2000, 2001, 2002 Free Software Foundation, Inc.

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2, or (at your option)
   any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place - Suite 330,
   Boston, MA 02111-1307, USA.  */

/* As a special exception, when this file is copied by Bison into a
   Bison output file, you may use that output file without restriction.
   This special exception was added by the Free Software Foundation
   in version 1.24 of Bison.  */

#ifndef BISON_ZEND_ZEND_INI_PARSER_H
# define BISON_ZEND_ZEND_INI_PARSER_H

/* Tokens.  */
#ifndef YYTOKENTYPE
# define YYTOKENTYPE
   /* Put the tokens into the symbol table, so that GDB and other debuggers
      know about them.  */
   enum yytokentype {
     TC_STRING = 258,
     TC_ENCAPSULATED_STRING = 259,
     BRACK = 260,
     SECTION = 261,
     CFG_TRUE = 262,
     CFG_FALSE = 263,
     TC_DOLLAR_CURLY = 264
   };
#endif
#define TC_STRING 258
#define TC_ENCAPSULATED_STRING 259
#define BRACK 260
#define SECTION 261
#define CFG_TRUE 262
#define CFG_FALSE 263
#define TC_DOLLAR_CURLY 264




#ifndef YYSTYPE
typedef int yystype;
# define YYSTYPE yystype
#endif




#endif /* not BISON_ZEND_ZEND_INI_PARSER_H */

