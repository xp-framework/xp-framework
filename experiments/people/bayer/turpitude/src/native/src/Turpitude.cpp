#include <Turpitude.h>

jobject zval_to_jobject(JNIEnv* env, zval* val) {
    if (NULL == val)
        return NULL;

    jclass cls = NULL;
    jobject obj = NULL;
    jmethodID mid = NULL;

    switch (Z_TYPE_P(val)) {
        case IS_LONG:
            printf("IS_LONG\n");
            cls = env->FindClass("java/lang/Long");
            mid = env->GetMethodID(cls, "<init>", "(J)V");
            obj = env->NewObject(cls, mid, val->value.lval);
            break;
        case IS_DOUBLE:
            printf("IS_DOUBLE\n");
            cls = env->FindClass("java/lang/Double");
            mid = env->GetMethodID(cls, "<init>", "(D)V");
            obj = env->NewObject(cls, mid, val->value.dval);
            break;
        case IS_BOOL:
            printf("IS_BOOL\n");
            cls = env->FindClass("java/lang/Boolean");
            mid = env->GetMethodID(cls, "<init>", "(Z)V");
            obj = env->NewObject(cls, mid, (val->value.lval)?true:false);
            break;
        case IS_ARRAY:
            printf("IS_ARRAY\n");
            break;
        case IS_OBJECT:
            printf("IS_OBJECT\n");
            break;
        case IS_CONSTANT:
        case IS_STRING:
            printf("IS_STRING or IS_CONSTANT\n");
            obj = env->NewStringUTF(val->value.str.val);
            break;
        default:
            // probably null
            obj = NULL;
    }

    return obj;
}

