/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

public class ProxyWrapper {
    public long identifier;
    public Object object;

    public ProxyWrapper(long identifier, Object object) {
        this.identifier= identifier;
        this.object= object;
    }
}
