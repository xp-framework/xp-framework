/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ServerContext;

/**
 * Handles initialization
 *
 */
public class InitializationDelegate implements Delegate {

    /**
     * No-arg constructor
     *
     * @access  public
     */
    public InitializationDelegate() {
    }

    /**
     * Constructor
     *
     * @access  public
     * @param   java.lang.String username
     * @param   java.lang.String password
     */
    public InitializationDelegate(final String username, final String password) {
        // FIXME: Not yet implemented
    }

    /**
     * Invocation handler
     *
     * @access  public
     * @param   net.xp_framework.easc.server.ServerContext ctx
     * @return  lang.Object
     */
    public Object invoke(ServerContext ctx) throws Exception {
        return true;
    }
}
