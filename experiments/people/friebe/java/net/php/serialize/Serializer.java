/* This class is part of the XP framework's Java extension
 *
 * $Id$ 
 */

package net.php.serialize;

  import java.lang.reflect.*;
  import java.util.HashMap;
  import java.util.ArrayList;
  import java.util.Arrays;
  import java.util.Date;
  import net.php.serialize.UnserializeException;

  /**
   * Serializer / unserializer for PHP serialized data
   *
   * Usage example:
   * <code>
   *   Object o= Serializer.unserialize("s:11:\"Hello World\";");
   *   System.out.println(o);
   * </code>
   *
   * Usage example:
   * <code>
   *   String s= Serializer.serialize("Hello");
   *   System.out.println(s);
   * </code>
   *
   * @see   http://php.net/unserialize
   * @see   http://php.net/serialize
   */
  public class Serializer {
  
    /**
     * Serializes a string
     *
     * @access  public
     * @param   String s
     * @return  String the serialized representation
     */
    public static String serialize(String s) {
      return "s:" + s.length() + ":\"" + s + "\";";
    }

    /**
     * Serializes a date object
     *
     * @access  public
     * @param   Date d
     * @return  String the serialized representation
     */
    public static String serialize(Date d) {
      return "O:4:\"Date\":1:{s:6:\"_utime\";i:" + (d.getTime() / 1000) + ";}";
    }

    /**
     * Serializes an boolean
     *  
     * @access  public
     * @param   boolean b 
     * @return  String the serialized representation
     */
    public static String serialize(boolean b) {
      return "b:" + b + ";";
    } 
    
    /**
     * Serializes an int
     *
     * @access  public
     * @param   int i
     * @return  String the serialized representation
     */
    public static String serialize(int i) {
      return "i:" + i + ";";
    }

    /**
     * Serializes a long
     *
     * @access  public
     * @param   long l
     * @return  String the serialized representation
     */
    public static String serialize(long l) {
      return "i:" + l + ";";
    }

    /**
     * Serializes a double
     *
     * @access  public
     * @param   double d
     * @return  String the serialized representation
     */
    public static String serialize(double d) {
      return "d:" + d + ";";
    }

    /**
     * Serializes a float
     *
     * @access  public
     * @param   float f
     * @return  String the serialized representation
     */
    public static String serialize(float f) {
      return "d:" + f + ";";
    }
    
    /**
     * Retrieve a list of all class fields of a class (including the ones
     * declared in super classes).
     *
     * @access  protected
     * @param   Class c
     * @return  Field[]
     */
    protected static Field[] classFields(Class c) {
      ArrayList list = new ArrayList();
      
      do {
        list.addAll(Arrays.asList(c.getDeclaredFields()));
      } while ((c= c.getSuperclass()) != null);
      
      return (Field[])list.toArray(new Field[0]);
    }
    
    /**
     * Serializes an object
     *
     * @access  public
     * @param   Object o the object to serialize
     * @return  String the serialized representation
     */
    public static String serialize(Object o) {
      if (o == null) {    // Catch bordercase
        return "N;";
      }
      
      StringBuffer buffer= new StringBuffer();
      Class c= o.getClass();
      Field[] fields= Serializer.classFields(c);
      
      buffer.append("O:");
      buffer.append(c.getName().length());
      buffer.append(":\"");
      buffer.append(c.getName());
      buffer.append("\":");
      buffer.append(fields.length);
      buffer.append(":{");
      
      for (int i= 0; i < fields.length; i++) {
        buffer.append("s:");
        buffer.append(fields[i].getName().length());
        buffer.append(":\"");
        buffer.append(fields[i].getName());
        buffer.append("\";");
        
        Object value= null; 
        try {
          fields[i].setAccessible(true);
          Class fieldClass = fields[i].getType();

System.err.println(c.getName() + ": Field #" + i + " '" + fields[i].toString());

          if (fieldClass == int.class) {
            buffer.append(Serializer.serialize(fields[i].getInt(o))); 
          } else if (fieldClass == long.class) {
            buffer.append(Serializer.serialize(fields[i].getLong(o)));
          } else if (fieldClass == boolean.class) { 
            buffer.append(Serializer.serialize(fields[i].getBoolean(o)));
          } else if (fieldClass == float.class) {
            buffer.append(Serializer.serialize(fields[i].getFloat(o)));
          } else if (fieldClass == double.class) {
            buffer.append(Serializer.serialize(fields[i].getDouble(o)));
          } else if (fieldClass == String.class) {
            buffer.append(Serializer.serialize((String)fields[i].get(o)));
          } else if (fieldClass == Date.class) {
            buffer.append(Serializer.serialize((Date)fields[i].get(o)));
          } else {
            buffer.append(Serializer.serialize(fields[i].get(o)));
          }
        } catch (IllegalAccessException e) {
          System.err.println(e.toString());
          buffer.append("N;"); 
        }
      }
      
      buffer.append("}");
      return buffer.toString();
    }
    
    /**
     * Unserializes an object
     *
     * @access  public
     * @param   String str the serialized representation
     * @return  Object the unserialized object
     */
    public static Object unserialize(String str) {
      return null; // TBI 
    }
  }
