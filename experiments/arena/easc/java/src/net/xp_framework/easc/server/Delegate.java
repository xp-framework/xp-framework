/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.ProxyMap;

public interface Delegate {
    public Object invoke(ProxyMap map) throws Exception;
}
