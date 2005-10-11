/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import java.lang.reflect.Proxy;
import javax.naming.InitialContext;
import net.xp_framework.easc.server.ServerContext;

public class LookupDelegate implements Delegate {
    String jndiName;

    public LookupDelegate(String jndiName) {
        this.jndiName= jndiName;
    }

    public Object invoke(ServerContext ctx) throws Exception {
        return (new InitialContext()).lookup(this.jndiName);
    }
}
