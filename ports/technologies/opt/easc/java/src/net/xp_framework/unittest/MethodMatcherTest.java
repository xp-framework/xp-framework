/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Ignore;
import net.xp_framework.unittest.Person;
import java.lang.reflect.Method;

import static net.xp_framework.easc.util.MethodMatcher.methodFor;
import static org.junit.Assert.*;

/**
 * Test method matching functionality. 
 *
 * Note: This is a JUnit 4 testcase!
 *
 * @see   net.xp_framework.easc.util.MethodMatcher
 */
public class MethodMatcherTest {

    /**
     * Returns a string representation of a method
     *
     * Format:
     *   [returnType] [methodName]:[numArguments]([optArgumentList])
     *
     * Notes:
     * - returnType is the return type's class name
     * - optArgumentList is a comma-separated list of argument class names
     *
     * Returns the string "(null)" if the specified argument is NULL
     *
     * @see     java.lang.reflect.Method#toString
     * @access  protected
     * @param   java.lang.reflect.Method m
     * @return  java.lang.String
     */
    protected String methodString(Method m) {
        if (m == null) return "(null)";   // Catch border-case, prevent NPE

        Class[] parameters= m.getParameterTypes();
        StringBuffer buf= new StringBuffer()
            .append(m.getReturnType().getName())
            .append(' ')
            .append(m.getName())
            .append(':')
            .append(parameters.length)
            .append("(");
        
        // Append parameter types
        if (parameters.length > 0) {  
            for (Class c: parameters) {
                buf.append(c.getName()).append(", ");
            }
            buf.delete(buf.length() - 2, buf.length());
        }
        
        return buf.append(')').toString();
    }

    /**
     * Ensures getId() will be found when search for no-arg version
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void noArgMethod() throws Exception {
        assertEquals(
            "int getId:0()", 
            methodString(methodFor(Person.class, "getId", new Object[] { }))
        );
    }

    /**
     * Ensures setName() will not be found when supplying a 
     * java.lang.String as sole argument.
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void oneArgMethod() throws Exception {
        assertEquals(
            "void setName:1(java.lang.String)", 
            methodString(methodFor(Person.class, "setName", new Object[] { "New name" }))
        );
    }

    /**
     * Ensures setName() will also be found when supplying null
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void oneArgMethodWithNullArg() throws Exception {
        assertEquals(
            "void setName:1(java.lang.String)", 
            methodString(methodFor(Person.class, "setName", new Object[] { null }))
        );
    }

    /**
     * Ensures setId() will be found when supplying a java.lang.Integer
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void onePrimitiveArgMethod() throws Exception {
        assertEquals(
            "void setId:1(int)", 
            methodString(methodFor(Person.class, "setId", new Object[] { new Integer(1) }))
        );
    }

    /**
     * Ensures no method will be found when supplying a non-existant
     * method name.
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void methodNotFound() throws Exception {
        assertEquals(
            "(null)", 
            methodString(methodFor(Person.class, "nonExistantName", new Object[] { }))
        );
    }

    /**
     * Ensures setName() will not be found when supplying a java.lang.Integer
     * instead of a java.lang.String
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void argumentsMismatch() throws Exception {
        assertEquals(
            "(null)", 
            methodString(methodFor(Person.class, "setName", new Object[] { new Integer(1) }))
        );
    }

    /**
     * Ensures setId() will not be found when supplying a java.lang.Long
     *
     * @access  public
     * @throws  java.lang.Exception
     */
    @Test public void primitiveArgumentsMismatch() throws Exception {
        assertEquals(
            "(null)", 
            methodString(methodFor(Person.class, "setId", new Object[] { new Long(1) }))
        );
    }
    
    @Test public void stringArray() throws Exception {
        assertEquals(
            "void setResponsibilities:1([Ljava.lang.String;)", 
            methodString(methodFor(Person.class, "setResponsibilities", new Object[] { 
                new String[] { "Hello" }
            }))
        );
    }
    
    @Test @Ignore("Not sure how to do this ATM") public void personObjectArray() throws Exception {
        assertEquals(
            "void setFriends:1([Lnet.xp_framework.unittest.Person;)", 
            methodString(methodFor(Person.class, "setFriends", new Object[] { 
                new Object[] { new Person() { } }
            }))
        );
    }

    /**
     * Checks for varargs method (System.out.printf())
     *
     * @access  public
     * @throws  java.lang.Exception
     */    
    @Test public void varargsMethod() {
        Class c= System.out.getClass();
        assertEquals(
            "java.io.PrintStream printf:2(java.lang.String, [Ljava.lang.Object;)",
            methodString(methodFor(c, "printf", new Object[] { "More %s", new Object[] { "Power" }}))
        );
        assertEquals(
            "java.io.PrintStream printf:2(java.lang.String, [Ljava.lang.Object;)",
            methodString(methodFor(c, "printf", new Object[] { "%s bytes", new Object[] { 1 }}))
        );
        assertEquals(
            "java.io.PrintStream printf:2(java.lang.String, [Ljava.lang.Object;)",
            methodString(methodFor(c, "printf", new Object[] { "Nothing to see here", new Object[] { }}))
        );
    }
}
