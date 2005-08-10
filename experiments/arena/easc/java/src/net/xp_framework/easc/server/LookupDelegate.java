/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import javax.naming.Context;
import javax.naming.InitialContext;
import java.util.Properties;

public class LookupDelegate implements Delegate {
    String jndiName;

    public LookupDelegate(String jndiName) {
        this.jndiName= jndiName;
    }

    public Object invoke() throws Exception {
        return (new InitialContext()).lookup(this.jndiName);
    }
}
