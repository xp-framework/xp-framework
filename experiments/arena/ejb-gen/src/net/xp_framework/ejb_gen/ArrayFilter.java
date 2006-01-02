/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

package net.xp_framework.ejb_gen;

import java.util.ArrayList;

/**
 * Generic array filter
 *
 * Example:
 * <code>
 *   import java.lang.reflect.Method;
 *
 *   // Set up filter as anonymous class
 *   ArrayFilter<Method> webmethods= new ArrayFilter<Method>() {
 *       protected boolean yield(Method method) {
 *           return method.isAnnotationPresent(method, WebMethod.class);
 *       }
 *   };
 *
 *   // Iterate over filtered array
 *   for (Method m: webmethods.filter(clazz.getMethods())) {
 *      System.out.println("WebMethod: " + m);
 *   }
 * </code>
 *
 * Works on java.util.ArrayList and arrays.
 */
abstract public class ArrayFilter<T> {

    /**
     * Yield method - called for each element in the specified list.
     * Return TRUE if the passed element should be included in the
     * filtered list, FALSE otherwise
     *
     * @access  protected
     * @param   T element
     * @return  boolean
     */
    abstract protected boolean yield(T element);

    /**
     * Filters an ArrayList
     *
     * @access  public
     * @param   java.util.ArrayList<T> elements
     * @return  java.util.ArrayList<T> the filtered list
     */
    public ArrayList<T> filter(ArrayList<T> elements) {
        ArrayList<T> filtered= new ArrayList<T>();
        for (int i = 0; i < elements.size(); i++) {
            T element= elements.get(i);
            if (this.yield(element)) filtered.add(element);
        }
        return filtered;
    }
    
    /**
     * Filters an array
     *
     * @access  public
     * @param   T[] elements
     * @return  java.util.ArrayList<T> the filtered list
     */
    public ArrayList<T> filter(T[] elements) {
        ArrayList<T> filtered= new ArrayList<T>();
        for (int i = 0; i < elements.length; i++) {
            if (this.yield(elements[i])) filtered.add(elements[i]);
        }
        return filtered;
    }
}
