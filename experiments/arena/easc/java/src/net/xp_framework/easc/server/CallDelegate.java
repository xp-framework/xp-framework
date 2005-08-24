/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.lang.reflect.Proxy;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ProxyMap;
import static net.xp_framework.easc.util.MethodMatcher.methodFor;

public class CallDelegate implements Delegate {
    private long objectId;
    private String methodName;
    private Object[] arguments;

    public CallDelegate(long objectId, String methodName, Object[] arguments) {
        this.objectId= objectId;
        this.methodName= methodName;
        this.arguments= arguments;
    }

    public Object invoke(ProxyMap map) throws Exception {
        Object instance= map.getObject(this.objectId);
        Method method= methodFor(instance.getClass(), this.methodName, this.arguments);
        
        if (null == method) {
            throw new NoSuchMethodException("Method '" + this.methodName + "' not found");
        }
        
        Object result= method.invoke(instance, this.arguments);
        
        // If the result is a reference to a proxy, add it to our proxy list
        if (null != result && Proxy.isProxyClass(result.getClass())) {
            long identifier= map.put("(null)", result);
            return new ProxyWrapper(identifier, result);
        }

        return result;
    }
}
