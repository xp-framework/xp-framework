/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

#define define_exception_type(etype)                            \
    struct exception_context {                                  \
        jmp_buf *penv;                                          \
        int caught;                                             \
        volatile struct { etype etmp; } v;                      \
    }

#define init_exception_context(ec) ((void)((ec)->penv = 0))

#define try                                                     \
    {                                                           \
        jmp_buf *exception__prev, exception__env;               \
        exception__prev = the_exception_context->penv;          \
        the_exception_context->penv = &exception__env;          \
        if (setjmp(exception__env) == 0) {                      \
            if (&exception__prev)

#define exception__catch(action)                                \
            else { }                                            \
            the_exception_context->caught = 0;                  \
        } else {                                                \
            the_exception_context->caught = 1;                  \
        }                                                       \
        the_exception_context->penv = exception__prev;          \
    }                                                           \
    if (!the_exception_context->caught || action) { }           \
    else

#define catch(e) exception__catch(((e) = the_exception_context->v.etmp, 0))
#define catch_anonymous exception__catch(0)

#define throw(msg)                                              \
    for (;; longjmp(*the_exception_context->penv, 1)) {         \
        e.message= msg;                                         \
        the_exception_context->v.etmp= e;                       \
    }

#define new

#define Exception(x) x

struct exception {
    char* message;
};

define_exception_type(struct exception);

struct exception_context the_exception_context[1];
struct exception e;
