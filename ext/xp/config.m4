dnl $Id$
dnl config.m4 for extension xp

PHP_ARG_ENABLE(xp, whether to enable xp support,
[  --enable-xp           Enable xp support])

if test "$PHP_XP" != "no"; then
  PHP_NEW_EXTENSION(xp, xp.c, $ext_shared)
fi
