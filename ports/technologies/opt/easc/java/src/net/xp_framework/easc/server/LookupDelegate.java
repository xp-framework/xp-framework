/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import java.lang.reflect.Proxy;
import javax.naming.InitialContext;
import net.xp_framework.easc.server.ServerContext;

/**
 * Handles JNDI lookups
 *
 */
public class LookupDelegate implements Delegate {
    private String jndiName;

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.String jndiName
     */
    public LookupDelegate(String jndiName) {
        this.jndiName= jndiName;
    }

    /**
     * Invocation handler
     *
     * @access  public
     * @param   net.xp_framework.easc.server.ServerContext ctx
     * @return  lang.Object
     */
    public Object invoke(ServerContext ctx) throws Exception {
        return (new InitialContext()).lookup(this.jndiName);
    }

    /**
     * Return a classloader to be used instead of the current one
     *
     */
    public ClassLoader getClassLoader() {
        return null;
    }
}
