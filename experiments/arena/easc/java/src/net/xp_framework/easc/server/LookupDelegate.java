/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import java.lang.reflect.Proxy;
import javax.naming.InitialContext;
import net.xp_framework.easc.server.ProxyMap;
import net.xp_framework.easc.server.ProxyWrapper;

public class LookupDelegate implements Delegate {
    String jndiName;

    public LookupDelegate(String jndiName) {
        this.jndiName= jndiName;
    }

    public Object invoke(ProxyMap map) throws Exception {
        Object o= (new InitialContext()).lookup(this.jndiName);
        
        if (Proxy.isProxyClass(o.getClass())) {
            long identifier= map.put(this.jndiName, o);
            return new ProxyWrapper(identifier, o);
        }
        
        return o;
    }
}
