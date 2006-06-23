/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.stateless;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import javax.naming.InitialContext;
import java.rmi.RemoteException;
import java.util.Arrays;
import net.xp_framework.beans.stateful.RemoteIterator;
import net.xp_framework.beans.stateful.RemoteIteratorHome;

/**
 * Iterator demonstration
 *
 * @ejb.bean
 *      name="IteratorDemo"
 *      type="Stateless"
 *      view-type="both"
 *      local-jndi-name="xp/demo/IteratorDemo"
 *      jndi-name="xp/demo/IteratorDemo"
 *      display-name="IteratorDemo demonstration"
 */
public abstract class IteratorDemoBean implements SessionBean {
    protected RemoteIteratorHome iteratorHome;
    
    /**
     * Retrieve an IteratorDemo for a given list
     *
     * @ejb.interface-method view-type="both"
     * @access  public
     * @param   Object[] list
     * @return  RemoteIterator
     */
    public RemoteIterator iterateOn(Object[] objects) throws Exception {
        return iteratorHome.create((java.util.List)Arrays.asList(objects));
    }
    
    /**
     * Session context injection method
     *
     * @access  public
     */
    public void setSessionContext(SessionContext sessionContext) {
        try {
            InitialContext c= new InitialContext();
            this.iteratorHome= (RemoteIteratorHome)c.lookup("xp/demo/RemoteIterator");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
