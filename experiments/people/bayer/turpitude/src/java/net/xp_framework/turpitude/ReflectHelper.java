package net.xp_framework.turpitude;

import java.lang.reflect.Method;
import java.lang.reflect.Field;
import java.lang.reflect.InvocationTargetException;
import java.util.HashMap;

/**
 * This class contains helper methods for the java reflection api
 */
public class ReflectHelper {
    private static HashMap<Class<?>, Class<?>> wrapperTypes= new HashMap<Class<?>, Class<?>>();

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

    /**
     *  tries to set a staticfield value on an object, using the supplied arguments
     */
    public static void setStaticFieldValue(Class cls, String fieldname, Object val) throws NoSuchFieldException,
                                                                                           IllegalAccessException {
        Field f = cls.getField(fieldname);
        f.set(cls, val);
    }

    /**
     *  tries to retrieve a static field value from a class, using the supplied arguments
     */
    public static Object getStaticFieldValue(Class cls, String fieldname) throws NoSuchFieldException,
                                                                                 IllegalAccessException {
        Field f = cls.getField(fieldname);

        return f.get(cls);
    }

    /**
     *  tries to set a field value on an object, using the supplied arguments
     */
    public static void setFieldValue(Object obj, String fieldname, Object val) throws NoSuchFieldException,
                                                                                      IllegalAccessException {
        Field f = obj.getClass().getField(fieldname);
        f.set(obj, val);
    }

    /**
     *  tries to retrieve a field value on an object, using the supplied arguments
     */
    public static Object getFieldValue(Object obj, String fieldname) throws NoSuchFieldException,
                                                                            IllegalAccessException {
        Field f = obj.getClass().getField(fieldname);

        return f.get(obj);
    }

    /**
     *  tries to call a method on an object, using the supplied arguments
     */
    public static Object callMethod(Object obj, String methodname, Object... args) throws NoSuchMethodException, 
                                                                                          IllegalAccessException,
                                                                                          InvocationTargetException {
        //System.out.println("callMethod  mname = " + methodname + " " + obj.getClass().getName());
        //System.out.println("array: " + args.length);
        //for (int i=0; i<args.length; i++)
        //    System.out.println("   => " + args[i]);

        Method m = ReflectHelper.findMethod(obj.getClass(), methodname, args);
        if (m == null)
            throw new NoSuchMethodException("unable to find matching method " + methodname + " in class " +  obj.getClass().getName());
        
        Object retval = null;
        retval = m.invoke(obj, args);

        return retval;
    }

    /**
     * tries to find a matching method in the given class.
     * blatantly copied from net.xp_framework.easc.util.MethodMatcher
     */
    public static Method findMethod(Class c, String name, Object... args) {
        for (Method m : c.getMethods()) {

            // First, check name equalitiy 
            if (!name.equals(m.getName())) continue;

            // Check signatures vs. arguments
            //System.out.println("[MethodMatcher] checking " + m);
            if (!signatureMatchesArguments(m.getParameterTypes(), args)) continue;

            // Now we have a candidate!
            return m;
        }

        // We can't find anything...
        return null;
    }

    private static boolean signatureMatchesArguments(Class[] signature, Object... arguments) {

        // Check argument length against signature length            
        if (signature.length != arguments.length) {
            //System.out.println("[MethodMatcher] signature.length(" + signature.length + ") != arguments.length(" + arguments.length + ")");
            return false;
        }

        // Check argument types vs. signature types
        int offset= 0;
        for (Class<?> c : signature) {
            if (!(null == arguments[offset] || 
                 ((c.isPrimitive() ? wrapperTypes.get(c) : c).isAssignableFrom(arguments[offset].getClass())))) {
                //System.out.println("[MethodMatcher] signature argument #" + offset + ": " + c.getName() + " is not assignable from " + arguments[offset].getClass().getName());
                return false;
            }
            offset++;
        }

        return true;
    }


}
