/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.stateless;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import java.rmi.RemoteException;

import java.util.Date;

/**
 * Roundtrip demonstration
 *
 * @ejb.bean
 *      name="Roundtrip"
 *      type="Stateless"
 *      view-type="both"
 *      local-jndi-name="xp/demo/Roundtrip"
 *      jndi-name="xp/demo/Roundtrip"
 *      display-name="Roundtrip Demo"
 */
public class RoundtripBean implements SessionBean {

    /**
     * Echoes the given string argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   java.lang.String s
     * @return  java.lang.String the given string
     */
    public String echoString(String s) {
        return s;
    }

    /**
     * Echoes the given int argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   int i
     * @return  int given int
     */
    public int echoInt(int i) {
        return i;
    }

    /**
     * Echoes the given double argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   double d
     * @return  double given double
     */
    public double echoDouble(double d) {
        return d;
    }

    /**
     * Echoes the given boolean argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   boolean b
     * @return  boolean given boolean
     */
    public boolean echoBool(boolean b) {
        return b;
    }

    /**
     * Echoes the given null argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   java.lang.Object nullref
     * @return  java.lang.Object given nullref
     */
    public Object echoNull(Object nullref) {
        return nullref;
    }

    /**
     * Echoes the given Date argument
     *
     * @ejb.interface-method view-type = "both"
     * @param   java.util.Date d
     * @return  java.util.Date given date
     */
    public Date echoDate(Date d) {
        return d;
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
