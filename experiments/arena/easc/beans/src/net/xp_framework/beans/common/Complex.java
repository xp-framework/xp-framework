/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.common;

import java.io.Serializable;

/**
 * Complex numbers class
 *
 */
public class Complex implements Serializable {
    protected int real;
    protected int imag;

    /**
     * No-arg constructor
     *
     * @access  public
     */
    public Complex() { }
  
    /**
     * Constructor
     *
     * @access  public
     * @param   int real
     * @param   int imag
     */
    public Complex(int real, int imag) {
        this.real= real;
        this.imag= imag;
    }
    
    /**
     * Adds two complex numbers
     *
     * @static
     * @access  public
     * @param   net.xp_framework.beans.common.Complex c1
     * @param   net.xp_framework.beans.common.Complex c2
     * @return  net.xp_framework.beans.common.Complex
     */
    public static Complex add(Complex c1, Complex c2) {
        return new Complex(c1.real + c2.real, c1.imag + c2.imag);
    }
}
