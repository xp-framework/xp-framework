/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.stateless;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import java.rmi.RemoteException;

import net.xp_framework.beans.common.Complex;

/**
 * Calculator demonstration
 *
 * @ejb.bean
 *      name="Calculator"
 *      type="Stateless"
 *      view-type="both"
 *      jndi-name="xp/Calculator"
 *		display-name="Calculator Demo"
 * @ejb.util
 *      generate="physical"
 */
public class CalculatorBean implements SessionBean {

    /**
     * Adds two floating point numbers 
     *
     * @ejb.interface-method view-type = "both"
     * @param   float a
     * @param   float b
     * @return  float the sum of the given parameters a and b
     */
    public float add(float a, float b) {
        return a + b;
    }

    /**
     * Adds two integers
     *
     * @ejb.interface-method view-type = "both"
     * @param   int a
     * @param   int b
     * @return  int the sum of the given parameters a and b
     */
    public int add(int a, int b) {
        return a + b;
    }

    /**
     * Adds two complex numbers
     *
     * @ejb.interface-method view-type = "both"
     * @param   net.xp_framework.beans.common.Complex a
     * @param   net.xp_framework.beans.common.Complex b
     * @return  net.xp_framework.beans.common.Complex
     */
    public Complex add(Complex a, Complex b) {
        return Complex.add(a, b);
    }

    /**
     * Subtracts two floating point numbers 
     *
     * @ejb.interface-method view-type = "both"
     * @param   float a
     * @param   float b
     * @return  float the result of the subtraction of the given parameters a and b
     */
    public float subtract(float a, float b) {
        return a - b;
    }

    /**
     * Subtracts two integers
     *
     * @ejb.interface-method view-type = "both"
     * @param   int a
     * @param   int b
     * @return  int the result of the subtraction of the given parameters a and b
     */
    public int subtract(int a, int b) {
        return a - b;
    }

    /**
     * Multiplies two floating point numbers 
     *
     * @ejb.interface-method view-type = "both"
     * @param   float a
     * @param   float b
     * @return  float the product of the given parameters a and b
     */
    public float multiply(float a, float b) {
        return a * b;
    }

    /**
     * Multiplies two integers
     *
     * @ejb.interface-method view-type = "both"
     * @param   int a
     * @param   int b
     * @return  int the product of the given parameters a and b
     */
    public int multiply(int a, int b) {
        return a * b;
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
