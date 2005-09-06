/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ProxyMap;

public class FinalizeDelegate implements Delegate {

    public FinalizeDelegate() {
    }

    public Object invoke(ProxyMap map) throws Exception {
        return true;
    }
}
