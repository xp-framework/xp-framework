dnl $Id$
dnl config.m4 for extension threads

PHP_ARG_ENABLE(threads, whether to enable threads support,
[  --enable-threads           Enable threads support])

if test "$PHP_THREADS" != "no"; then
  PHP_NEW_EXTENSION(threads, threads.c, $ext_shared)
  CFLAGS="$CFLAGS -pthread"
fi
