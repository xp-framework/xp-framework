/* This class is part of the XP framework's Java extension
 *
 * $Id$ 
 */

package net.php.serialize;

import java.lang.reflect.*;
import java.util.HashMap;
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
      Field[] fields= c.getFields();
      
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
          value= fields[i].get(o);
        } catch (IllegalAccessException e) {
          value= null;
        }
        
        buffer.append(Serializer.serialize(value));
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
