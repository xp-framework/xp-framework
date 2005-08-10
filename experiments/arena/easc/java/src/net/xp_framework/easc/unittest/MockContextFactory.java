/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.unittest;

import java.util.Hashtable;
import javax.naming.Context;
import javax.naming.spi.InitialContextFactory;
import net.xp_framework.easc.unittest.MockContext;

/**
 * Mock context factory. Returns a MockContext
 *
 * @see   net.xp_framework.easc.unittest.ServerTest
 */
public class MockContextFactory implements InitialContextFactory {

    /**
     * Creates an Initial Context for beginning name resolution.
     *
     * @access  public
     * @param   java.util.Hashtable<?, ?> environment
     * @return  javax.naming.Context
     */
    public Context getInitialContext(Hashtable<?, ?> environment) {
        return new MockContext();
    }
}
