/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.lang.reflect.Proxy;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ProxyMap;

public class CallDelegate implements Delegate {
    private Object instance;
    private Method method;
    private Object[] arguments;

    public CallDelegate(Object instance, Method method, Object[] arguments) {
        this.instance= instance;
        this.method= method;
        this.arguments= arguments;
    }

    public Object invoke(ProxyMap map) throws Exception {
        Object result= this.method.invoke(this.instance, this.arguments);
        
        // If the result is a reference to a proxy, add it to our proxy list
        if (null != result && Proxy.isProxyClass(result.getClass())) {
            long identifier= map.put("(null)", result);
            return new ProxyWrapper(identifier, result);
        }

        return result;
    }
    
    /**
     * Creates a string representation of this delegate
     *
     * @access  public
     * @return  java.lang.String
     */
    @Override public String toString() {
        return (
            this.getClass().getName() + 
            "@(instance= " + this.instance +
            ", method= " + this.method + 
            ", args= " + this.arguments.length + 
            "))"
        );
    }
}
