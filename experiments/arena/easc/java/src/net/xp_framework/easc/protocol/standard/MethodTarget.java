/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

import net.xp_framework.easc.protocol.standard.Invokeable;
import net.xp_framework.easc.protocol.standard.SerializerContext;
import java.lang.reflect.Method;

public class MethodTarget<Return, Parameter> implements Invokeable<Return, Parameter> {
    private Method method = null;
    private char token;

    public MethodTarget(Method m, char t) {
        this.method= m;
        this.token= t;
    }

    public String toString() {
        return this.method.toString();
    }

    public char token() {
        return this.token;
    }

    public Return invoke(Parameter p, Object arg) throws Exception {
        return (Return)this.method.invoke(null, new Object[] { p, arg });
    }
}
