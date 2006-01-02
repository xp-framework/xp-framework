/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

package net.xp_framework.ejb_gen;

import java.util.ArrayList;

/**
 * Generic array imploder
 *
 * @see   http://php3.de/implode
 * Works on java.util.ArrayList and arrays.
 */
abstract public class ArrayImploder<T> {

    /**
     * Yield method - called for each element in the specified list.
     *
     * @access  protected
     * @param   T element
     * @return  java.lang.String
     */
    abstract protected String yield(T element);

    /**
     * Filters an ArrayList
     *
     * @access  public
     * @param   java.util.ArrayList<T> elements
     * @return  java.util.ArrayList<T> the filtered list
     */
    public String implode(String separator, ArrayList<T> elements) {
        StringBuffer s= new StringBuffer();
        for (int i = 0; i < elements.size(); i++) {
            s.append(this.yield(elements.get(i)));
            if (i < elements.size() - 1) s.append(separator);
        }
        return s.toString();
    }
    
    /**
     * Filters an array
     *
     * @access  public
     * @param   T[] elements
     * @return  java.util.ArrayList<T> the filtered list
     */
    public String implode(String separator, T[] elements) {
        StringBuffer s= new StringBuffer();
        for (int i = 0; i < elements.length; i++) {
            s.append(this.yield(elements[i]));
            if (i < elements.length - 1) s.append(separator);
        }
        return s.toString();
    }
}
