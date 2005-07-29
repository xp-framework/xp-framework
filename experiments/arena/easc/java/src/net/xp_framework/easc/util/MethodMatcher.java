/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.util;

import java.lang.reflect.Method;
import java.util.HashMap;

/**
 * Matches methods by given argument list
 *
 */
public class MethodMatcher {

    private static HashMap<Class, Class> wrapperTypes= new HashMap<Class, Class>();
    
    static {
        wrapperTypes.put(char.class, Character.class);
        wrapperTypes.put(byte.class, Byte.class);
        wrapperTypes.put(short.class, Short.class);
        wrapperTypes.put(int.class, Integer.class);
        wrapperTypes.put(long.class, Long.class);
        wrapperTypes.put(double.class, Double.class);
        wrapperTypes.put(float.class, Float.class);
        wrapperTypes.put(boolean.class, Boolean.class);
    }

    private static boolean signatureMatchesArguments(Class[] signature, Object[] arguments) {

        // Check argument length against signature length            
        if (signature.length != arguments.length) return false;

        // Check argument types vs. signature types
        int offset= 0;
        for (Class c : signature) {
            if (!((c.isPrimitive() ? wrapperTypes.get(c) : c).isAssignableFrom(arguments[offset++].getClass()))) return false;
        }
        
        return true;
    }

    public static Method methodFor(Class c, String name, Object[] arguments) {
        for (Method m : c.getMethods()) {

            // First, check name equalitiy
            if (!name.equals(m.getName())) continue;

            // Check signatures vs. arguments
            if (!signatureMatchesArguments(m.getParameterTypes(), arguments)) continue;
            
            // Now we have a candidate!
            return m;
        }
        
        // We can't find anything...
        return null;
    }
}
