/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.lang.reflect.Proxy;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ServerContext;

public class CallDelegate implements Delegate {
    private Object instance;
    private Method method;
    private Object[] arguments;

    public CallDelegate(Object instance, Method method, Object[] arguments) {
        this.instance= instance;
        this.method= method;
        this.arguments= arguments;
    }

    public Object invoke(ServerContext ctx) throws Exception {
        return this.method.invoke(this.instance, this.arguments);
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

    /**
     * Return a classloader to be used instead of the current one
     *
     */
    public ClassLoader getClassLoader() {
        return this.instance.getClass().getClassLoader();
    }
}
