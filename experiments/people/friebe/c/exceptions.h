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
        exception__prev = _exception_context->penv;             \
        _exception_context->penv = &exception__env;             \
        if (setjmp(exception__env) == 0) {                      \
            if (&exception__prev)

#define exception__catch(action)                                \
            else { }                                            \
            _exception_context->caught = 0;                     \
        } else {                                                \
            _exception_context->caught = 1;                     \
        }                                                       \
        _exception_context->penv = exception__prev;             \
    }                                                           \
    if (!_exception_context->caught || action) { }              \
    else

#define catch(e) exception__catch(((e) = _exception_context->v.etmp, 0))

#define throw(msg)                                              \
    for (;; longjmp(*_exception_context->penv, 1)) {            \
        e.message= msg;                                         \
        _exception_context->v.etmp= e;                          \
    }

#define new
#define Exception

struct exception {
    char* message;
};

struct exception_context _exception_context[1];
struct exception e;

define_exception_type(struct exception);
