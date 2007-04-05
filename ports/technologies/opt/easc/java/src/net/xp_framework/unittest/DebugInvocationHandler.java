/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;

/**
 * Invocation handler that returns a debug message for any method called on it
 * 
 * @see   net.xp_framework.unittest.ServerTest
 */
public class DebugInvocationHandler implements InvocationHandler {
    
    /**
     * Handles invokations
     *
     * @see     java.lang.reflect.InvocationHandler#invoke
     * @access  public
     * @param   java.lang.Object proxy
     * @param   java.lang.reflect.Method method
     * @param   java.lang.Object[] args
     * @return  java.lang.Object
     */
    public Object invoke(Object proxy, Method method, Object[] args) {
        if ("hashCode".equals(method.getName())) {
            return 1;
        }

        return (
            "Invoked method " + method.toString() + 
            " with " + (null == args ? 0 : args.length) + " argument(s)"
        );
    }
}
