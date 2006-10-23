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

    static interface EJBHomeExt extends EJBHome { }

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
     * Helper method
     *
     * @return  An anonymous EJBHome instance
     */
    protected EJBHomeExt anonymousEjbHomeExtInstance() {
        return new EJBHomeExt() { 
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
            public String toString() {
              return "Invokeable<" + EJBHome.class.getName() + ">";
            }
            public String invoke(EJBHome p, Object arg) throws Exception {
                return arg.toString();
            }
        });
        assertTrue(invokeableFor(c) instanceof Invokeable);
        assertEquals("OK", ((Invokeable)invokeableFor(c)).invoke(i, new String("OK")));
        
        // Unregister, should be unknown again (= use default invokeable)
        unregisterMapping(EJBHome.class);
        assertEquals(null, invokeableFor(c));
    }

    /**
     * Tests 
     *
     */
    @Test public void ejbHomeAndEjbHomeExtClass() throws Exception {
        EJBHomeExt ix= this.anonymousEjbHomeExtInstance();
        Class cx= ix.getClass();
        EJBHome i= this.anonymousEjbHomeInstance();
        Class c= i.getClass();

        // Before: Unknown (= use default invokeable)
        assertEquals(null, invokeableFor(cx));
        assertEquals(null, invokeableFor(c));
        
        // Map EJBHomeExt and EJBHome
        registerMapping(EJBHomeExt.class, new Invokeable<String, EJBHomeExt>() {
            public String toString() {
              return "Invokeable<" + EJBHomeExt.class.getName() + ">";
            }
            public String invoke(EJBHomeExt p, Object arg) throws Exception {
                return arg.toString();
            }
        });
        registerMapping(EJBHome.class, new Invokeable<String, EJBHome>() {
            public String toString() {
              return "Invokeable<" + EJBHome.class.getName() + ">";
            }
            public String invoke(EJBHome p, Object arg) throws Exception {
                return arg.toString();
            }
        });
        assertTrue(invokeableFor(cx) instanceof Invokeable);
        assertEquals("EXT", ((Invokeable)invokeableFor(cx)).invoke(ix, new String("EXT")));
        assertTrue(invokeableFor(c) instanceof Invokeable);
        assertEquals("OK", ((Invokeable)invokeableFor(c)).invoke(i, new String("OK")));
        
        // Unregister, should be unknown again (= use default invokeable)
        unregisterMapping(EJBHomeExt.class);
        unregisterMapping(EJBHome.class);
        assertEquals(null, invokeableFor(cx));
        assertEquals(null, invokeableFor(c));
    }
}
