/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$ 
 */

package net.xp_framework.easc.protocol.standard;

import java.lang.reflect.Proxy;
import java.lang.reflect.Field;
import java.lang.reflect.Modifier;
import java.lang.reflect.Method;
import java.lang.reflect.InvocationHandler;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.AbstractCollection;
import java.util.Date;
import java.util.Iterator;
import net.xp_framework.easc.protocol.standard.Handler;
import net.xp_framework.easc.protocol.standard.ArraySerializer;
import net.xp_framework.easc.protocol.standard.Invokeable;
import net.xp_framework.easc.protocol.standard.SerializationException;

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
    
    private static class MethodTarget<Return, Parameter> implements Invokeable<Return, Parameter> {
        private Method method = null;
        
        MethodTarget(Method m) {
            this.method= m;    
        }
        
        public Return invoke(Parameter p) throws Exception {
            return (Return)this.method.invoke(null, new Object[] { p });
        }
    }

    private static class Length {
        public int value = 0;

        public Length(int initial) {
            this.value = initial;
        }
        
        @Override public String toString() {
            return "Length(" + this.value + ")";
        }
    }
    
    private static enum Token {
        T_NULL {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                length.value= 2;
                return null;
            }
        },

        T_BOOLEAN {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                length.value= 4; 
                return ('1' == serialized.charAt(2));
            }
        },

        T_INTEGER {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               return Integer.parseInt(value);
            }
        },

        T_LONG {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               return Long.parseLong(value);
            }
        },

        T_FLOAT {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Float.parseFloat(value);
            }
        },

        T_DOUBLE {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Double.parseDouble(value);
            }
        },

        T_STRING {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String strlength= serialized.substring(2, serialized.indexOf(':', 2));
                int offset= 2 + strlength.length() + 2;
                int parsed= Integer.parseInt(strlength);

                length.value= offset + parsed + 2;
                return serialized.substring(offset, parsed+ offset); 

            }
        },

        T_HASH {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String arraylength= serialized.substring(2, serialized.indexOf(':', 2));
                int parsed= Integer.parseInt(arraylength);
                int offset= arraylength.length() + 2 + 2;
                HashMap h= new HashMap(parsed);

                for (int i= 0; i < parsed; i++) {
                    Object key= Serializer.valueOf(serialized.substring(offset), length, loader);
                    offset+= length.value;
                    Object value= Serializer.valueOf(serialized.substring(offset), length, loader);
                    offset+= length.value;
                    
                    h.put(key, value);
                }

                length.value= offset + 1;
                return h;
            }
        },

        T_ARRAY {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String arraylength= serialized.substring(2, serialized.indexOf(':', 2));
                int parsed= Integer.parseInt(arraylength);
                int offset= arraylength.length() + 2 + 2;
                Object[] array= new Object[parsed];

                for (int i= 0; i < parsed; i++) {
                    array[i]= Serializer.valueOf(serialized.substring(offset), length, loader);
                    offset+= length.value;
                }

                length.value= offset + 1;
                return array;
            }
        },
        
        T_OBJECT {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String classnamelength= serialized.substring(2, serialized.indexOf(':', 2));
                int offset= classnamelength.length() + 2 + 2;
                int parsed= Integer.parseInt(classnamelength);
                Class c= null;
                Object instance= null;

                // Load class
                try {
                    c= loader.loadClass(serialized.substring(offset, parsed+ offset)); 
                } catch (ClassNotFoundException e) {
                    throw new SerializationException(loader + ": " + e.getMessage());
                }
                
                // Instanciate
                instance= c.newInstance();
                
                String objectlength= serialized.substring(parsed+ offset+ 2, serialized.indexOf(':', parsed+ offset+ 2));
                offset+= parsed+ 2 + objectlength.length() + 2;
                
                // Set field values
                for (int i= 0; i < Integer.parseInt(objectlength); i++) {
                    Field f= c.getDeclaredField((String)Serializer.valueOf(serialized.substring(offset), length, loader));
                    offset+= length.value;
                    Object value= Serializer.valueOf(serialized.substring(offset), length, loader);
                    offset+= length.value;
                    
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
                }

                length.value= offset + 1;
                return instance;
            }
        },

        T_DATE {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
               String value= serialized.substring(2, serialized.indexOf(';', 2));

               length.value= value.length() + 3;
               return new Date(Long.parseLong(value) * 1000);
            }
        },

        T_BYTE {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
                String value= serialized.substring(2, serialized.indexOf(';', 2));

                length.value= value.length() + 3;
                return Byte.parseByte(value);
            }
        },

        T_SHORT {
            public Object handle(String serialized, Length length, ClassLoader loader) throws Exception { 
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
      
        abstract public Object handle(String serialized, Length length, ClassLoader loader) throws Exception;
    }

    private static HashMap<Class, Invokeable<?, ?>> typeMap= new HashMap<Class, Invokeable<?, ?>>();
    private static HashMap<Class, String> exceptionMap= new HashMap<Class, String>();
    
    static {
        
        // Set up typeMap by inspecting all class methods with @Handler annotation
        for (Method m : Serializer.class.getDeclaredMethods()) {
            if (!m.isAnnotationPresent(Handler.class)) continue;
            
            registerMapping(m.getParameterTypes()[0], new MethodTarget<String, Object>(m));
        }
        
        registerExceptionName(IllegalArgumentException.class, "IllegalArgument");
        registerExceptionName(IllegalAccessException.class, "IllegalAccess");
        registerExceptionName(ClassNotFoundException.class, "ClassNotFound");
        registerExceptionName(NullPointerException.class, "NullPointer");
    }
    
    public static void registerExceptionName(Class c, String name) {
        exceptionMap.put(c, name);
    }
    
    public static void registerMapping(Class c, Invokeable<?, ?> i) {
        typeMap.put(c, i);
    }
    
    public static Invokeable<?, ?> invokeableFor(Class c) {
        Invokeable<?, ?> i= null;
        if (null != (i= typeMap.get(c))) return i;    // Direct hit
        
        // Search for classes the specified class is assignable from
        for (Class key: typeMap.keySet()) {
            if (!key.isAssignableFrom(c)) continue;

            // Cache results. Next time around, we'll have a direct hit
            i= typeMap.get(key);
            typeMap.put(c, i);
            return i;
        }
        
        // Nothing found, return NULL. This will make representationOf()
        // use the default object serialization mechanism (field-based)
        return null;
    }

    private static ArrayList<Field> classFields(Class c) {
        ArrayList<Field> list= new ArrayList<Field>();
        
        for (Field f : c.getDeclaredFields()) {
            if (Modifier.isTransient(f.getModifiers())) continue;
            list.add(f);
        }
        
        return list;
    }

    private static String representationOf(Object o, Invokeable i) throws Exception {
        if (i != null) return (String)i.invoke(o);
        if (null == o) return "N;";

        // Default object serialization
        StringBuffer buffer= new StringBuffer();
        Class c= o.getClass();
        long numFields = 0;

        for (Field f : classFields(c)) {
            buffer.append("s:");
            buffer.append(f.getName().length());
            buffer.append(":\"");
            buffer.append(f.getName());
            buffer.append("\";");

            f.setAccessible(true);
            buffer.append(representationOf(f.get(o), invokeableFor(f.getType())));
            numFields++;
        }

        buffer.append("}");        
        buffer.insert(0, "O:" + c.getName().length() + ":\"" + c.getName() + "\":" + numFields + ":{");
        return buffer.toString();
    }

    @Handler public static String representationOf(String s) {
        if (null == s) return "N;";
        return "s:" + s.length() + ":\"" + s + "\";";
    } 

    @Handler public static String representationOf(char c) {
        return "s:1:\"" + c + "\";";
    }
    
    @Handler public static String representationOf(final char[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Character c) {
        if (null == c) return "N;";
        return "s:1:\"" + c + "\";";
    }

    @Handler public static String representationOf(byte b) {
        return "B:" + b + ";";
    }
    
    @Handler public static String representationOf(final byte[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Byte b) {
        if (null == b) return "N;";
        return "B:" + b + ";";
    }

    @Handler public static String representationOf(short s) {
        return "S:" + s + ";";
    }
    
    @Handler public static String representationOf(final short[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Short s) {
        if (null == s) return "N;";
        return "S:" + s + ";";
    }

    @Handler public static String representationOf(int i) {
        return "i:" + i + ";";
    }

    @Handler public static String representationOf(final int[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Integer i) {
        if (null == i) return "N;";
        return "i:" + i + ";";
    }

    @Handler public static String representationOf(long l) {
        return "l:" + l + ";";
    }

    @Handler public static String representationOf(final long[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Long l) {
        if (null == l) return "N;";
        return "l:" + l + ";";
    }

    @Handler public static String representationOf(double d) {
        return "d:" + d + ";";
    }
    
    @Handler public static String representationOf(final double[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Double d) {
        if (null == d) return "N;";
        return "d:" + d + ";";
    }

    @Handler public static String representationOf(float f) {
        return "f:" + f + ";";
    }
    
    @Handler public static String representationOf(final float[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Float f) {
        if (null == f) return "N;";
        return "f:" + f + ";";
    }

    @Handler public static String representationOf(boolean b) {
        return "b:" + (b ? 1 : 0) + ";";
    }

    @Handler public static String representationOf(final boolean[] array) throws Exception {
        return new ArraySerializer() {
            public void yield(int i) {
                this.buffer.append(representationOf(array[i]));
            }
        }.run(array.length);
    }

    @Handler public static String representationOf(Boolean b) {
        if (null == b) return "N;";
        return "b:" + (b ? 1 : 0) + ";";
    }

    @Handler public static String representationOf(HashMap h) throws Exception {
        if (null == h) return "N;";
        StringBuffer buffer= new StringBuffer("a:" + h.size() + ":{");
        
        for (Iterator it= h.keySet().iterator(); it.hasNext(); ) {
            Object key= it.next();
            Object value= h.get(key);

            buffer.append(representationOf(key, invokeableFor(key.getClass())));
            buffer.append(representationOf(value, invokeableFor(value.getClass())));
        }
        
        buffer.append("}");
        return buffer.toString();
    }

    @Handler public static String representationOf(AbstractCollection c) throws Exception {
        if (null == c) return "N;";
        return representationOf(c.toArray());
    }

    @Handler public static String representationOf(Date d) throws Exception {
        if (null == d) return "N;";
        return "T:" + d.getTime() / 1000 + ";";   // getTime() returns *milliseconds*
    }
    
    @Handler public static String representationOf(Object[] a) throws Exception {
        StringBuffer buffer= new StringBuffer("a:" + a.length + ":{");

        for (int i= 0; i < a.length; i++) {
            buffer.append("i:" + i + ";");
            buffer.append(representationOf(a[i], invokeableFor(a[i].getClass())));
        }

        buffer.append("}");
        return buffer.toString();
    }
    
    
    @Handler public static String representationOf(StackTraceElement e) throws Exception {
        if (null == e) return "N;";
        StringBuffer buffer= new StringBuffer();
        Class c= e.getClass();
        String name;
        
        buffer.append("t:4:{");
        buffer.append("s:4:\"file\";").append(representationOf(e.getFileName()));
        buffer.append("s:5:\"class\";").append(representationOf(e.getClassName()));
        buffer.append("s:6:\"method\";").append(representationOf(e.getMethodName()));
        buffer.append("s:4:\"line\";").append(representationOf(e.getLineNumber()));
        buffer.append("}");

        return buffer.toString();        
    }

    @Handler public static String representationOf(Throwable e) throws Exception {
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
        buffer.append("\":2:{s:7:\"message\";");
        buffer.append(representationOf(e.getMessage()));
        buffer.append("s:5:\"trace\";a:").append(trace.length).append(":{");

        int offset= 0;
        for (StackTraceElement element: trace) {
            buffer.append("i:").append(offset++).append(';').append(representationOf(element));
        }

        buffer.append("}}");
        return buffer.toString();        
    }
        
    /**
     * Fall-back method for default serialization. Not a handler since this 
     * would lead to an infinite loop in the invokeableFor() method.
     *
     * @static
     * @access  public
     * @param   java.lang.Object o
     * @return  java.lang.String
     */
    public static String representationOf(Object o) throws Exception {
        if (null == o) return "N;";
        return representationOf(o, invokeableFor(o.getClass()));
    }
    
    /**
     * Private helper method for public valueOf()
     *
     * @access  private
     * @param   java.lang.String serialized
     * @param   Length length
     * @return  java.lang.Object
     */
    private static Object valueOf(String serialized, Length length, ClassLoader loader) throws Exception {
        return Token.valueOf(serialized.charAt(0)).handle(serialized, length, loader);
    }

    private static Object valueOf(String serialized, Length length) throws Exception {
        return Token.valueOf(serialized.charAt(0)).handle(serialized, length, Serializer.class.getClassLoader());
    }

    public static Object valueOf(String serialized) throws Exception {
        return valueOf(serialized, new Length(0));
    }
    
    public static Object valueOf(String serialized, ClassLoader loader) throws Exception {
        return valueOf(serialized, new Length(0), loader);
    }
}
