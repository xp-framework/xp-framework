/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ServerContext;

public class FinalizeDelegate implements Delegate {

    public FinalizeDelegate() {
    }

    public Object invoke(ServerContext ctx) throws Exception {
        return true;
    }

    /**
     * Return a classloader to be used instead of the current one
     *
     */
    public ClassLoader getClassLoader() {
        return null;
    }
}
