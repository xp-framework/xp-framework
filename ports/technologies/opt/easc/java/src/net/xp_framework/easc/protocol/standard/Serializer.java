/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

import sun.reflect.ReflectionFactory;
import java.security.AccessController;
import java.lang.reflect.Proxy;
import java.lang.reflect.Field;
import java.lang.reflect.Modifier;
import java.lang.reflect.Method;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationHandler;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Collection;
import java.util.Date;
import java.util.Iterator;
import java.io.Serializable;
import net.xp_framework.easc.protocol.standard.Handler;
import net.xp_framework.easc.protocol.standard.Invokeable;
import net.xp_framework.easc.protocol.standard.SerializationException;
import net.xp_framework.easc.protocol.standard.MethodTarget;
import net.xp_framework.easc.protocol.standard.SerializerContext;

/**
 * Serializer / unserializer for PHP serialized data
 *
 * Usage example:
 * <code>
 *   Object o= Serializer.valueOf("s:11:\"Hello World\";");
 *   System.out.println(o);
 * </code>
 *
 * Usage example:
 * <code>
 *   String s= Serializer.representationOf("Hello");
 *   System.out.println(s);
 * </code>
 *
 * @see   http://php.net/unserialize
 * @see   http://php.net/serialize
 */
public class Serializer {
    
    private static class Length {
        public int value = 0;

        public Length(int initial) {
            this.value = initial;
        }
        
        @Override public String toString() {
            return "Length(" + this.value + ")";
        }
    }
    
    private static String getPackageName(Class cl) {
        String s = cl.getName();
        int i = s.lastIndexOf('[');
        if (i >= 0) {
            s = s.substring(i + 2);
        }
        i = s.lastIndexOf('.');
        return (i >= 0) ? s.substring(0, i) : "";
    }

    private static boolean packageEquals(Class cl1, Class cl2) {
        return (cl1.getClassLoader() == cl2.getClassLoader() &&
                getPackageName(cl1).equals(getPackageName(cl2)));
    }

    private static Constructor getSerializableConstructor(Class cl) {
        Class initCl= cl;

        while (Serializable.class.isAssignableFrom(initCl)) {
            if ((initCl = initCl.getSuperclass()) == null) {
                return null;
            }
        }
        try {
            Constructor cons = initCl.getDeclaredConstructor(new Class[0]);
            int mods = cons.getModifiers();
            if ((mods & Modifier.PRIVATE) != 0 ||
                ((mods & (Modifier.PUBLIC | Modifier.PROTECTED)) == 0 &&
                 !packageEquals(cl, initCl)))
            {
                return null;
            }
            
            ReflectionFactory reflFactory= (ReflectionFactory)AccessController.doPrivileged(
                new ReflectionFactory.GetReflectionFactoryAction()
            );

            cons = reflFactory.newConstructorForSerialization(cl, cons);
            cons.setAccessible(true);
            return cons;
        } catch (NoSuchMethodException ex) {
            return null;
        }
    }

    private static Field getFieldAddressOf(Class cl, String fieldName) throws NoSuchFieldException {
        Class initCl= cl;

        do {
            for (Field f: cl.getDeclaredFields()) {
                if (f.getName().equals(fieldName)) return f;
            }
        } while (null != (cl= cl.getSuperclass()));
        
        throw new NoSuchFieldException("Field " + fieldName + " not found in " + initCl.getName() + " (or any superclasses)");
    }
    
    /**
     * Workaround for "missing" java.lang.Class.hasMethod()
     *
     */
    private static Method findReadResolveMethod(Class c) {
        for (Method m: classMethods(c)) {
            if ("readResolve".equals(m.getName()) && 0 == m.getParameterTypes().length) {
                return m;
            }
        }
        return null;
    }
    
    private static enum Token {
        T_NULL {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                length.value= 2;
                return null;
            }
        },

        T_BOOLEAN {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                length.value= 4; 
                return ('1' == serialized.charAt(2));
            }
        },

        T_INTEGER {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               return Integer.parseInt(value);
            }
        },

        T_LONG {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               return Long.parseLong(value);
            }
        },

        T_FLOAT {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Float.parseFloat(value);
            }
        },

        T_DOUBLE {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Double.parseDouble(value);
            }
        },

        T_STRING {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String strlength= serialized.substring(2, serialized.indexOf(':', 2));
                int offset= 2 + strlength.length() + 2;
                int parsed= Integer.parseInt(strlength);

                length.value= offset + parsed + 2;
                return serialized.substring(offset, parsed+ offset); 

            }
        },

        T_HASH {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String arraylength= serialized.substring(2, serialized.indexOf(':', 2));
                int parsed= Integer.parseInt(arraylength);
                int offset= arraylength.length() + 2 + 2;
                HashMap h= new HashMap(parsed);

                for (int i= 0; i < parsed; i++) {
                    Object key= Serializer.valueOf(serialized.substring(offset), length, context, null);
                    offset+= length.value;
                    Object value= Serializer.valueOf(serialized.substring(offset), length, context, null);
                    offset+= length.value;
                    
                    h.put(key, value);
                }

                length.value= offset + 1;
                return h;
            }
        },

        T_ARRAY {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String arraylength= serialized.substring(2, serialized.indexOf(':', 2));
                int parsed= Integer.parseInt(arraylength);
                int offset= arraylength.length() + 2 + 2;
                Object[] array= new Object[parsed];

                for (int i= 0; i < parsed; i++) {
                    array[i]= Serializer.valueOf(serialized.substring(offset), length, context, null);
                    offset+= length.value;
                }

                length.value= offset + 1;
                return array;
            }
        },
        
        T_OBJECT {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String classnamelength= serialized.substring(2, serialized.indexOf(':', 2));
                int offset= classnamelength.length() + 2 + 2;
                int parsed= Integer.parseInt(classnamelength);
                Class c= null;
                Object instance= null;

                // Load class
                try {
                    c= context.classLoader.loadClass(serialized.substring(offset, parsed+ offset)); 
                } catch (ClassNotFoundException e) {
                    throw new SerializationException(context.classLoader + ": " + e.getMessage());
                }

                String objectlength= serialized.substring(parsed+ offset+ 2, serialized.indexOf(':', parsed+ offset+ 2));
                offset+= parsed+ 2 + objectlength.length() + 2;
                
                // Check to see if it is an enum and use Enum.valueOf() to instantiate
                if (c.isEnum()) {
                    if (!"1".equals(objectlength)) {
                      throw new SerializationException("Enums should be serialized as class:1:name");
                    }
                    Serializer.valueOf(serialized.substring(offset), length, context, null);    // "name"
                    offset+= length.value;
                    instance= Enum.valueOf(c, (String)Serializer.valueOf(serialized.substring(offset), length, context, String.class));
                    offset+= length.value;
                    length.value= offset + 1;

                    return instance;
                }
                
                // Instanciate
                Constructor ctor= Serializer.getSerializableConstructor(c);
                instance= (null == ctor) ? c.newInstance() : ctor.newInstance();
                
                // Set field values
                for (int i= 0; i < Integer.parseInt(objectlength); i++) {
                    Field f= Serializer.getFieldAddressOf(c, (String)Serializer.valueOf(serialized.substring(offset), length, context, null));
                    offset+= length.value;
                    Object value= Serializer.valueOf(serialized.substring(offset), length, context, f.getType());
                    offset+= length.value;
                    
                    try {
                        f.setAccessible(true);
                        if (f.getType() == char.class) {
                            f.setChar(instance, ((String)value).charAt(0));
                        } else if (f.getType() == byte.class) {
                            f.setByte(instance, ((Byte)value).byteValue());
                        } else if (f.getType() == short.class) {
                            f.setShort(instance, ((Short)value).shortValue());
                        } else if (f.getType() == int.class) {
                            f.setInt(instance, ((Integer)value).intValue());
                        } else if (f.getType() == long.class) {
                            f.setLong(instance, ((Long)value).longValue());
                        } else if (f.getType() == double.class) {
                            f.setDouble(instance, ((Double)value).doubleValue());
                        } else if (f.getType() == float.class) {
                            f.setFloat(instance, ((Float)value).floatValue());
                        } else if (f.getType() == boolean.class) {
                            f.setBoolean(instance, ((Boolean)value).booleanValue());
                        } else {
                            f.set(instance, value);
                        }
                    } catch (IllegalArgumentException e) {
                        throw new SerializationException(
                            "Illegal argument while setting " + c.getName() + "." + f + " to " + 
                            (null == value ? "(null)" : value.getClass().getName()) + ": " + 
                            e.getMessage()
                        );
                    } catch (IllegalAccessException e) {
                        throw new SerializationException(
                            "Access violation while setting " + c.getName() + "." + f + " to " + 
                            (null == value ? "(null)" : value.getClass().getName()) + ": " + 
                            e.getMessage()
                        );
                    }
                }

                length.value= offset + 1;
                
                // Check for a readResolve() method
                Method readResolve= Serializer.findReadResolveMethod(c);
                if (null != readResolve) {
                    try {
                        readResolve.setAccessible(true);
                        return readResolve.invoke(instance, new Object[] { });
                    } catch (IllegalAccessException e) {
                        throw new SerializationException(
                            e.getMessage() + " during call to " + c + ".readResolve()"
                        );
                    } catch (IllegalArgumentException e) {
                        throw new SerializationException(
                            e.getMessage() + " during call to " + c + ".readResolve()"
                        );
                    } catch (InvocationTargetException e) {
                        throw new SerializationException(
                            e.getMessage() + " during call to " + c + ".readResolve()"
                        );
                    }
                }
                
                
                return instance;
            }
        },

        T_DATE {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               if (null == clazz) {
                  return new Date(Long.parseLong(value) * 1000);
               } else {
                  return clazz.getConstructor(long.class).newInstance(Long.parseLong(value) * 1000);
               }
            }
        },

        T_BYTE {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Byte.parseByte(value);
            }
        },

        T_SHORT {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Short.parseShort(value);
            }
        };
      
        private static HashMap<Character, Token> map= new HashMap<Character, Token>();
      
        static {
            map.put('N', T_NULL);
            map.put('b', T_BOOLEAN);
            map.put('i', T_INTEGER);
            map.put('l', T_LONG);
            map.put('f', T_FLOAT);
            map.put('d', T_DOUBLE);
            map.put('s', T_STRING);
            map.put('a', T_HASH);
            map.put('A', T_ARRAY);
            map.put('O', T_OBJECT);
            map.put('T', T_DATE);
            map.put('B', T_BYTE);
            map.put('S', T_SHORT);
        }
      
        public static Token valueOf(char c) throws Exception {
            if (!map.containsKey(c)) {
                throw new SerializationException("Unknown type '" + c + "'");
            }
            return map.get(c);
        }
      
        abstract public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception;
    }

    private static HashMap<Class, Invokeable<?, ?>> typeMap= new HashMap<Class, Invokeable<?, ?>>();
    private static HashMap<Class, String> exceptionMap= new HashMap<Class, String>();
    
    static {
        
        // Set up typeMap by inspecting all class methods with @Handler annotation
        for (Method m : Serializer.class.getDeclaredMethods()) {
            if (!m.isAnnotationPresent(Handler.class)) continue;
            
            registerMapping(m.getParameterTypes()[0], new MethodTarget<String, Object>(m, m.getAnnotation(Handler.class).value()));
        }
        
        registerExceptionName(IllegalArgumentException.class, "IllegalArgument");
        registerExceptionName(IllegalAccessException.class, "IllegalAccess");
        registerExceptionName(ClassNotFoundException.class, "ClassNotFound");
        registerExceptionName(NullPointerException.class, "NullPointer");
    }
    
    public static void registerExceptionName(Class c, String name) {
        exceptionMap.put(c, name);
    }
    
    public static boolean hasMapping(Class c) {
        return typeMap.containsKey(c);
    }
    
    public static void registerMapping(Class c, Invokeable<?, ?> i) {
        typeMap.put(c, i);
    }

    public static void unregisterMapping(Class c) {
        typeMap.remove(c);
    }
    
    public static Invokeable<?, ?> invokeableFor(Class c) {
        Invokeable<?, ?> i= null;
        if (c.isInterface()) return null;
        if (null != (i= typeMap.get(c))) return i;    // Direct hit
        
        // Search for classes the specified class is assignable from
        for (Class key: typeMap.keySet()) {
            if (!key.isAssignableFrom(c)) continue;

            return typeMap.get(key);
        }
        
        // Nothing found, return NULL. This will make representationOf()
        // use the default object serialization mechanism (field-based)
        return null;
    }

    public static ArrayList<Field> classFields(Class c) {
        ArrayList<Field> list= new ArrayList<Field>();
        
        do {
            for (Field f : c.getDeclaredFields()) {
                int modifiers= f.getModifiers();
                if (Modifier.isTransient(modifiers) || Modifier.isStatic(modifiers)) continue;
                list.add(f);
            }
        } while (null != (c= c.getSuperclass()));
        
        return list;
    }

    public static ArrayList<Method> classMethods(Class c) {
        ArrayList<Method> list= new ArrayList<Method>();
        
        do {
            for (Method f : c.getDeclaredMethods()) {
                int modifiers= f.getModifiers();
                if (Modifier.isStatic(modifiers)) continue;
                list.add(f);
            }
        } while (null != (c= c.getSuperclass()));
        
        return list;
    }

    private static String representationOf(Object o, Invokeable i, SerializerContext context) throws Exception {
        if (null == o) return "N;";

        if (!(o instanceof Serializable)) {
            throw new SerializationException("Trying to serialize non-serializable object " + o + " via " + i);
        }
        
        // DEBUG System.out.println("Serializing " + o.getClass().getName() + " using " + i);

        if (i != null) return (String)i.invoke(o, context);

        // Default object serialization
        StringBuffer buffer= new StringBuffer();
        Class c= o.getClass();
        long numFields = 0;

        for (Field f : classFields(c)) {

            // DEBUG System.out.println(">> field " + f);
            buffer.append("s:");
            buffer.append(f.getName().length());
            buffer.append(":\"");
            buffer.append(f.getName());
            buffer.append("\";");

            f.setAccessible(true);
            Object fieldValue= f.get(o);
            buffer.append(null == fieldValue ? "N;" : representationOf(
                fieldValue, 
                invokeableFor(fieldValue.getClass()), 
                context
            ));
            numFields++;
        }

        buffer.append("}");        
        buffer.insert(0, "O:" + c.getName().length() + ":\"" + c.getName() + "\":" + numFields + ":{");
        return buffer.toString();
    }

    @Handler('s') protected static String representationOf(String s, SerializerContext context) {
        if (null == s) return "N;";
        return "s:" + s.length() + ":\"" + s + "\";";
    } 

    @Handler('s') protected static String representationOf(char c, SerializerContext context) {
        return "s:1:\"" + c + "\";";
    }
    
    @Handler('A') protected static String representationOf(char[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("s:1:\"" + array[i] + "\";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('s') protected static String representationOf(Character c, SerializerContext context) {
        if (null == c) return "N;";
        return "s:1:\"" + c + "\";";
    }

    @Handler('B') protected static String representationOf(byte b, SerializerContext context) {
        return "B:" + b + ";";
    }
    
    @Handler('A') protected static String representationOf(byte[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("B:" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('B') protected static String representationOf(Byte b, SerializerContext context) {
        if (null == b) return "N;";
        return "B:" + b + ";";
    }

    @Handler('S') protected static String representationOf(short s, SerializerContext context) {
        return "S:" + s + ";";
    }
    
    @Handler('A') protected static String representationOf(short[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("S:" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('s') protected static String representationOf(Short s, SerializerContext context) {
        if (null == s) return "N;";
        return "S:" + s + ";";
    }

    @Handler('i') protected static String representationOf(int i, SerializerContext context) {
        return "i:" + i + ";";
    }

    @Handler('A') protected static String representationOf(int[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("i:" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('i') protected static String representationOf(Integer i, SerializerContext context) {
        if (null == i) return "N;";
        return "i:" + i + ";";
    }

    @Handler('l') protected static String representationOf(long l, SerializerContext context) {
        return "l:" + l + ";";
    }

    @Handler('A') protected static String representationOf(long[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("l:" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('l') protected static String representationOf(Long l, SerializerContext context) {
        if (null == l) return "N;";
        return "l:" + l + ";";
    }

    @Handler('d') protected static String representationOf(double d, SerializerContext context) {
        return "d:" + d + ";";
    }
    
    @Handler('A') protected static String representationOf(double[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("d:" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('d') protected static String representationOf(Double d, SerializerContext context) {
        if (null == d) return "N;";
        return "d:" + d + ";";
    }

    @Handler('f') protected static String representationOf(float f, SerializerContext context) {
        return "f:" + f + ";";
    }
    
    @Handler('A') protected static String representationOf(float[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("f" + array[i] + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('f') protected static String representationOf(Float f, SerializerContext context) {
        if (null == f) return "N;";
        return "f:" + f + ";";
    }

    @Handler('b') protected static String representationOf(boolean b, SerializerContext context) {
        return "b:" + (b ? 1 : 0) + ";";
    }

    @Handler('A') protected static String representationOf(boolean[] array, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + array.length + ":{");
        for (int i= 0; i < array.length; i++) {
            buffer.append("b:" + (array[i] ? 1 : 0) + ";");
        }
        buffer.append('}');
        return buffer.toString();
    }

    @Handler('b') protected static String representationOf(Boolean b, SerializerContext context) {
        if (null == b) return "N;";
        return "b:" + (b ? 1 : 0) + ";";
    }

    @Handler('a') protected static String representationOf(HashMap h, SerializerContext context) throws Exception {
        if (null == h) return "N;";
        StringBuffer buffer= new StringBuffer("a:" + h.size() + ":{");
        
        for (Iterator it= h.keySet().iterator(); it.hasNext(); ) {
            Object key= it.next();
            Object value= h.get(key);

            buffer.append(representationOf(key, invokeableFor(key.getClass()), context));
            buffer.append(null == value 
                ? "N;" 
                : representationOf(value, invokeableFor(value.getClass()), context)
            );
        }

        buffer.append("}");
        return buffer.toString();
    }

    @Handler('A') protected static String representationOf(Collection c, SerializerContext context) throws Exception {
        if (null == c) return "N;";
        return representationOf(c.toArray(), invokeableFor(Object[].class), context);
    }

    @Handler('T') protected static String representationOf(Date d, SerializerContext context) throws Exception {
        if (null == d) return "N;";
        return "T:" + d.getTime() / 1000 + ";";   // getTime() returns *milliseconds*
    }
    
    @Handler('A') protected static String representationOf(Object[] a, SerializerContext context) throws Exception {
        StringBuffer buffer= new StringBuffer("A:" + a.length + ":{");

        for (int i= 0; i < a.length; i++) {
            buffer.append(null == a[i]
                ? "N;"
                : representationOf(a[i], invokeableFor(a[i].getClass()), context)
            );
        }

        buffer.append("}");
        return buffer.toString();
    }
    
    
    @Handler('t') protected static String representationOf(StackTraceElement e, SerializerContext context) throws Exception {
        if (null == e) return "N;";
        StringBuffer buffer= new StringBuffer();
        Class c= e.getClass();
        String name;
        
        buffer.append("t:4:{");
        buffer.append("s:4:\"file\";").append(representationOf(e.getFileName(), context));
        buffer.append("s:5:\"class\";").append(representationOf(e.getClassName(), context));
        buffer.append("s:6:\"method\";").append(representationOf(e.getMethodName(), context));
        buffer.append("s:4:\"line\";").append(representationOf(e.getLineNumber(), context));
        buffer.append("}");

        return buffer.toString();        
    }

    @Handler('e') protected static String representationOf(Throwable e, SerializerContext context) throws Exception {
        if (null == e) return "N;";
        StringBuffer buffer= new StringBuffer();
        Class c= e.getClass();
        StackTraceElement[] trace= e.getStackTrace();
        String alias= null;
        
        if (null != (alias= exceptionMap.get(c))) {
            buffer.append("e:").append(alias.length()).append(":\"").append(alias);
        } else {
            buffer.append("E:").append(c.getName().length()).append(":\"").append(c.getName());
        }
        
        // Message
        buffer.append("\":3:{s:7:\"message\";");
        buffer.append(representationOf(e.getMessage(), context));
        
        // Stacktrace
        int offset= 0;
        buffer.append("s:5:\"trace\";a:").append(trace.length).append(":{");
        for (StackTraceElement element: trace) {
            buffer.append("i:").append(offset++).append(';').append(representationOf(element, context));
        }

        // Cause
        buffer.append("}s:5:\"cause\";").append(representationOf(e.getCause()));
        
        return buffer.append("}").toString();        
    }
    
    @Handler('i') protected static String representationOf(Enum e, SerializerContext context) throws Exception {
        if (null == e) return "N;";
        
        Class c= e.getClass();
        String n= e.name();
        return new StringBuffer()
            .append("O:")
            .append(c.getName().length())
            .append(":\"")
            .append(c.getName())
            .append("\":1:{s:4:\"name\";s:")
            .append(n.length())
            .append(":\"")
            .append(n)
            .append("\";}")
            .toString()
        ;
    }

    /**
     * Serializes Class objects.
     *
     * @static
     * @access  public
     * @param   java.lang.Class c
     * @return  java.lang.String
     */
    @Handler('C') protected static String representationOf(Class c, SerializerContext context) throws Exception {
        if (null == c) return "N;";
        if (void.class.equals(c)) return "N;";

        // See if we know this type
        MethodTarget<?, ?> t= (MethodTarget<?, ?>)invokeableFor(c);
        if (null != t) {
            return "c:" + t.token() + ";";
        }
        
        // Fall back to generic class representation
        return "C:" + c.getName().length() + ":\"" + c.getName() + "\";";
    }
        
    /**
     * Fall-back method for default serialization. Not a handler since this 
     * would lead to an infinite loop in the invokeableFor() method.
     *
     * @static
     * @access  public
     * @param   java.lang.Object o
     * @param   net.xp_framework.easc.protocol.standard.SerializerContext context
     * @return  java.lang.String
     */
    public static String representationOf(Object o, SerializerContext context) throws Exception {
        if (null == o) return "N;";
        return representationOf(o, invokeableFor(o.getClass()), context);
    }

    /**
     * Fall-back method for default serialization.
     *
     * @static
     * @access  public
     * @param   java.lang.Object o
     * @return  java.lang.String
     */
    public static String representationOf(Object o) throws Exception {
        if (null == o) return "N;";
        return representationOf(o, invokeableFor(o.getClass()), new SerializerContext(Serializer.class.getClassLoader()));
    }
    
    /**
     * Private helper method for public valueOf()
     *
     * @static
     * @access  private
     * @param   java.lang.String serialized
     * @param   Length length
     * @param   net.xp_framework.easc.protocol.standard.SerializerContext context
     * @param   java.lang.Class clazz
     * @return  java.lang.Object
     */
    private static Object valueOf(String serialized, Length length, SerializerContext context, Class clazz) throws Exception {
        return Token.valueOf(serialized.charAt(0)).handle(serialized, length, context, clazz);
    }

    /**
     * Deserialize a string
     *
     * @static
     * @access  public
     * @param   java.lang.String serialized
     * @param   Length length
     * @return  java.lang.Object
     */
    public static Object valueOf(String serialized) throws Exception {
        return valueOf(serialized, new Length(0), new SerializerContext(Serializer.class.getClassLoader()), null);
    }

    /**
     * Deserialize a string to a specific class
     *
     * @static
     * @access  public
     * @param   java.lang.String serialized
     * @param   java.lang.Class clazz
     * @param   Length length
     * @return  java.lang.Object
     */
    public static Object valueOf(String serialized, Class clazz) throws Exception {
        return valueOf(serialized, new Length(0), new SerializerContext(Serializer.class.getClassLoader()), clazz);
    }
    
    /**
     * Deserialize a string using the specified context
     *
     * @static
     * @access  public
     * @param   java.lang.String serialized
     * @param   Length length
     * @param   net.xp_framework.easc.protocol.standard.SerializerContext context
     * @return  java.lang.Object
     */
    public static Object valueOf(String serialized, SerializerContext context) throws Exception {
        return valueOf(serialized, new Length(0), context, null);
    }

    /**
     * Deserialize a string using the specified context
     *
     * @static
     * @access  public
     * @param   java.lang.String serialized
     * @param   Length length
     * @param   net.xp_framework.easc.protocol.standard.SerializerContext context
     * @param   java.lang.Class clazz
     * @return  java.lang.Object
     */
    public static Object valueOf(String serialized, SerializerContext context, Class clazz) throws Exception {
        return valueOf(serialized, new Length(0), context, clazz);
    }
}
