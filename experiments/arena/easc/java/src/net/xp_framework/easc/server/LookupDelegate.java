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
        Properties env= new Properties();
		env.setProperty(Context.INITIAL_CONTEXT_FACTORY, "org.jnp.interfaces.NamingContextFactory");
		env.setProperty(Context.URL_PKG_PREFIXES, "org.jboss.naming:org.jnp.interfaces");
		env.setProperty(Context.PROVIDER_URL, "jnp://ia.schlund.de:1099");     
        
        return (new InitialContext(env)).lookup(this.jndiName);
    }
}
