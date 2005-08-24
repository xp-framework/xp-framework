/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.unittest;

import org.junit.Test;
import net.xp_framework.easc.unittest.NullInvocationHandler;
import net.xp_framework.easc.unittest.DebugInvocationHandler;
import net.xp_framework.easc.unittest.ITest;
import java.lang.reflect.Proxy;
import java.lang.reflect.InvocationHandler;

import static org.junit.Assert.*;

/**
 * Test invocation handlers
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.unittest.NullInvocationHandler
 * @see   net.xp_framework.easc.unittest.DebugInvocationHandler
 */
public class InvocationHandlerTest {

    /**
     * Helper that creates an ITest proxy with the specified handler
     *
     * @access  protected
     * @param   java.lang.reflect.InvocationHandler handler
     * @return  net.xp_framework.easc.unittest.ITest proxy
     */
    protected ITest createTestProxy(InvocationHandler handler) {
        return (ITest)Proxy.newProxyInstance(
            ITest.class.getClassLoader(),
            new Class[] { ITest.class },
            handler
        );
    }

    /**
     * Tests NullInvocationHandler
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void nullHandlerInvokeHello() throws Exception {
        assertEquals(
            null, 
            this.createTestProxy(new NullInvocationHandler()).hello()
        );
        assertEquals(
            null, 
            this.createTestProxy(new NullInvocationHandler()).hello("World")
        );
    }

    /**
     * Tests DebugInvocationHandler
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void debugHandlerInvokeHello() throws Exception {
        ITest proxy= this.createTestProxy(new DebugInvocationHandler());
        assertEquals(
            "Invoked method public abstract java.lang.Object net.xp_framework.easc.unittest.ITest.hello() with 0 argument(s)", 
            proxy.hello()
        );
        assertEquals(
            "Invoked method public abstract java.lang.Object net.xp_framework.easc.unittest.ITest.hello(java.lang.String) with 1 argument(s)", 
            proxy.hello("World")
        );
    }
}
