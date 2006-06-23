/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.stateful;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import java.rmi.RemoteException;
import java.util.Iterator;
import java.util.List;

/**
 * Remote iterator
 *
 * @ejb.bean
 *      name="RemoteIterator"
 *      type="Stateful"
 *      view-type="both"
 *      local-jndi-name="xp/demo/RemoteIterator"
 *      jndi-name="xp/demo/RemoteIterator"
 *      display-name="RemoteIterator"
 */
public class RemoteIteratorBean implements SessionBean {
    protected transient Iterator iterator;

    /**
     * Retrieve whether more elements exists
     *
     * @ejb.interface-method view-type = "both"
     * @access  public
     * @return  boolean
     */
    public boolean hasNext() throws Exception {
        return this.iterator.hasNext();
    }

    /**
     * Retrieve next element
     *
     * @ejb.interface-method view-type = "both"
     * @access  public
     * @return  Object
     */
    public Object next() throws Exception {
        return this.iterator.next();
    }

    /**
     * Create method
     *
     * @ejb.create-method
     * @access  public
     * @param   List values the values to iterate on
     */
    public void ejbCreate(List values) {
        this.iterator= values.iterator();
    }
    
    /**
     * Activate method
     *
     * @access  public
     */
    public void ejbActivate() throws EJBException, RemoteException { }

    /**
     * Passivate method
     *
     * @access  public
     */
    public void ejbPassivate() throws EJBException, RemoteException { }

    /**
     * Remove method
     *
     * @access  public
     */
    public void ejbRemove() throws EJBException, RemoteException { }

    /**
     * Session context injection method
     *
     * @access  public
     */
    public void setSessionContext(SessionContext sessionContext) throws EJBException, RemoteException { }
}
