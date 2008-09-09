/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ServerContext;
import org.jboss.security.SecurityAssociation;
import java.security.Principal;

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
    
        // FIXME: This should really be using the JAAS API. For some reason,
        // this doesn't work, the username gets lost somewhere inbetween,
        // resulting in a Security exception "Bad password for username=null".
        //
        // The implementation below uses a hard-coded version of what the 
        // JAAS api does, namely attaching them to the current thread (and
        // reading them from there at the time it needs them).
        // 
        // Problem here is we use a JBoss-specific version of this mechanism
        // - see above imports for SecurityAssociation
        SecurityAssociation.setPrincipal(new Principal() {
            public boolean equals(Object cmp) { 
                return cmp instanceof Principal && this.getName().equals(((Principal)cmp).getName()); 
            }

            public String getName() { 
                return username; 
            }

            public int hashCode() { 
                return username.hashCode(); 
            }

            public String toString() { 
                return "Principal(" + username + ")"; 
            }
        });
        SecurityAssociation.setCredential(password.toCharArray());
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

    /**
     * Return a classloader to be used instead of the current one
     *
     */
    public ClassLoader getClassLoader() {
        return null;
    }
}
