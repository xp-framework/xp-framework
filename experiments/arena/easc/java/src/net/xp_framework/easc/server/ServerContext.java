/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.util.HashMap;
import javax.transaction.UserTransaction;
import net.xp_framework.easc.server.Delegate;

abstract public class ServerContext {
    public HashMap<Integer, Object> objects= new HashMap<Integer, Object>();
    public UserTransaction transaction= null;
    
    /**
     * Retrieve lookup delegate
     * 
     * @abstract
     * @access  public
     * @param   java.lang.String jndiName
     * @return  net.xp_framework.easc.server.Delegate
     */
    abstract public Delegate lookup(String jndiName);
}
