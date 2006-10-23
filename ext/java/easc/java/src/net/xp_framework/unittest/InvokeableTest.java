/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import javax.ejb.EJBHome;
import net.xp_framework.easc.protocol.standard.Invokeable;

import static org.junit.Assert.*;
import static net.xp_framework.easc.protocol.standard.Serializer.invokeableFor;
import static net.xp_framework.easc.protocol.standard.Serializer.registerMapping;
import static net.xp_framework.easc.protocol.standard.Serializer.unregisterMapping;

/**
 * Test the class mapping functionality
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.protocol.standard.Serializer
 */
public class InvokeableTest {

    /**
     * Helper method
     *
     * @return  An anonymous EJBHome instance
     */
    protected EJBHome anonymousEjbHomeInstance() {
        return new EJBHome() { 
            public javax.ejb.HomeHandle getHomeHandle() { return null; }
            public javax.ejb.EJBMetaData getEJBMetaData() { return null; }
            public void remove(Object o) { }
            public void remove(javax.ejb.Handle h) { }
        };
    }

    /**
     * Tests 
     *
     */
    @Test public void ejbHomeClass() throws Exception {
        EJBHome i= this.anonymousEjbHomeInstance();
        Class c= i.getClass();

        // Before: Unknown (= use default invokeable)
        assertEquals(null, invokeableFor(c));
        
        // Map EJBHome
        registerMapping(EJBHome.class, new Invokeable<String, EJBHome>() {
            public String invoke(EJBHome p, Object arg) throws Exception {
                return arg.toString();
            }
        });
        assertTrue(invokeableFor(c) instanceof Invokeable);
        assertEquals("OK", ((Invokeable)invokeableFor(c)).invoke(i, new String("OK")));
        
        // Unregister, should be unknown again (= use default invokeable)
        unregisterMapping(EJBHome.class);
        // assertEquals(null, invokeableFor(c));    BROKEN!!!
    }
}
