/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

package net.xp_framework.ejb_gen.beans;

import net.xp_framework.ejb_gen.Stateless;
import net.xp_framework.ejb_gen.InterfaceMethod;

import static net.xp_framework.ejb_gen.ViewType.*;

@Stateless(name= "xp/demo/Calculator")
public class CalculatorBean {
    protected long calls= 0;

    /**
     * Returns the numbers of times this bean's methods have been called
     *
     * @access  public
     * @return  long
     */
    @InterfaceMethod(viewTypes= { Local }) 
    public long calls() {
        return this.calls;
    }

    /**
     * Adds two floating point numbers 
     *
     * @param   float a
     * @param   float b
     * @return  float the sum of the given parameters a and b
     */
    @InterfaceMethod(viewTypes= { Remote, Local }) 
    public float add(float a, float b) {
        this.calls++;
        return a + b;
    }

    /**
     * Adds two integer numbers 
     *
     * @param   int a
     * @param   int b
     * @return  int the sum of the given parameters a and b
     */
    @InterfaceMethod(viewTypes= { Remote, Local }) 
    public int add(int a, int b) {
        this.calls++;
        return a + b;
    }

    /**
     * Divide two integer numbers 
     *
     * @param   int a
     * @param   int b
     * @return  int the result of the division of a 
     */
    @InterfaceMethod(viewTypes= { Remote, Local }) 
    public int divide(int a, int b) throws IllegalArgumentException {
        this.calls++;
        
        if (b == 0) {
            throw new IllegalArgumentException("Divisor may not be zero");
        }
        return a / b;
    }
}
