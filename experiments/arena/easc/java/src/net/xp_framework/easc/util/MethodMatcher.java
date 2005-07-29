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

    public static Method methodFor(Class c, String name, Object[] arguments) {
        for (Method m : c.getMethods()) {

            // First, check name equalitiy
            if (!name.equals(m.getName())) continue;

            // Check argument length against signature length            
            Class[] parameters= m.getParameterTypes();
            if (parameters.length != arguments.length) continue;
            
            // Now we have a candidate!
            return m;
        }
        
        // We can't find anything...
        return null;
    }
}
