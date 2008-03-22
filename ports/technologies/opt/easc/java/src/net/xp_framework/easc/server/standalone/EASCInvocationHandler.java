/**
 * This file is part of the XP framework
 *
 * $Id$
 */
package net.xp_framework.easc.server.standalone;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;

public class EASCInvocationHandler implements InvocationHandler {
    private Object instance = null;

    /**
     * Constructor
     *
     */
    public EASCInvocationHandler(Object instance) {
        this.instance = instance;
    }

    /**
     * Handles invokations
     * 
     * @see     java.lang.reflect.InvocationHandler#invoke
     * @access  public
     * @param   java.lang.Object
     * @param   java.lang.reflect.Method
     * @param   java.lang.Object[]
     * @return  java.lang.Object
     */
    public Object invoke(Object proxy, Method method, Object[] args) throws Exception {
        return this.instance.getClass().getMethod(
            method.getName(), 
            method.getParameterTypes()
        ).invoke(this.instance, args);
    }
}
