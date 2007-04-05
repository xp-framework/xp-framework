/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.server.LookupDelegate;

/**
 * Server context for the invocation server (EASC mbean)
 *
 */
public class InvocationServerContext extends ServerContext {
    
    /**
     * Retrieve lookup delegate
     * 
     * @access  public
     * @param   java.lang.String jndiName
     * @return  net.xp_framework.easc.server.Delegate
     */
    public Delegate lookup(String jndiName) {
        return new LookupDelegate(jndiName);
    }
}
