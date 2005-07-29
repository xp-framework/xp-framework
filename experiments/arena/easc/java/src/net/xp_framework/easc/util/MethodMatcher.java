/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.util;

import java.lang.reflect.Method;

/**
 * Matches methods by given argument list
 *
 */
public class MethodMatcher {

    private static boolean signatureMatchesArguments(Class[] signature, Object[] arguments) {

        // Check argument length against signature length            
        if (signature.length != arguments.length) return false;

        // Check argument types vs. signature types
        int offset= 0;
        for (Class c : signature) {
            if (!c.equals(arguments[offset++].getClass())) return false;
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
