package net.xp_framework.turpitude;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;

/**
 * redirects method-invocations to a PHPObject hopefully providing that method
 */
public class PHPInvocationHandler implements InvocationHandler {
    private PHPObject MyObject = null;
    private PHPCompiledScript MyScript = null;

    public PHPInvocationHandler(PHPCompiledScript s) {
        MyScript = s;
    }

    public PHPInvocationHandler(PHPCompiledScript s, PHPObject o) {
        MyObject = o;
        MyScript = s;
    }

    public Object invoke(Object proxy, Method method, Object... args) throws Throwable {
        return MyScript.invokeMethod(MyObject, method.getName(), args);
    }

}
