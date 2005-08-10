/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;

public class InitializationDelegate implements Delegate {

    public InitializationDelegate() {
    }

    public Object invoke() throws Exception {
        return true;
    }
}
