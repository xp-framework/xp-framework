#include <net_xp_framework_turpitude_PHPObject.h>
#include "Turpitude.h"

JNIEXPORT void JNICALL Java_net_xp_1framework_turpitude_PHPObject_destroy(JNIEnv *env, jobject thiz) {
    zval* val = getZvalPtr(env, thiz);
    ZVAL_DELREF(val);
}


