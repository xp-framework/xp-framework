#ifndef __TURPITUDE_TYPES_H__
#define __TURPITUDE_TYPES_H__

enum turpitude_java_type {
    JAVA_UNKNOWN        = 0,
    JAVA_VOID           = 1,
    JAVA_OBJECT         = 2,
    JAVA_BOOLEAN        = 3,
    JAVA_BYTE           = 4,
    JAVA_CHAR           = 5,
    JAVA_SHORT          = 6,
    JAVA_INT            = 7,
    JAVA_LONG           = 8,
    JAVA_FLOAT          = 9,
    JAVA_DOUBLE         = 10,
    JAVA_ARRAY          = 1024,
    JAVA_OBJECT_ARRAY   = 1026,
    JAVA_BOOLEAN_ARRAY  = 1027,
    JAVA_BYTE_ARRAY     = 1028,
    JAVA_CHAR_ARRAY     = 1029,
    JAVA_SHORT_ARRAY    = 1030,
    JAVA_INT_ARRAY      = 1031,
    JAVA_LONG_ARRAY     = 1032,
    JAVA_FLOAT_ARRAY    = 1033,
    JAVA_DOUBLE_ARRAY   = 1034
};

/** returns the turpitude_java_type from a JNI field signature */
turpitude_java_type get_java_field_type(const char* sig);

inline bool turpitude_is_java_array(turpitude_java_type t) {
    return ((t & JAVA_ARRAY) == JAVA_ARRAY);
}

#endif
