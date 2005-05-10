package net.php.serialize;

import java.lang.reflect.*;
import java.util.ArrayList;

  /**
   * Exports any variable in a readable format
   *
   * Usage example:
   * <code>
   *   // ...
   *   System.out.println(Exporter.export(object));
   * </code>
   *
   * @see   http://php.net/var_export
   */
  public class Exporter {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */  
    public static String export(String s, String indent) {
      return "'" + s + "'";
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(int i, String indent) {
      return String.valueOf(i);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(long l, String indent) {
      return String.valueOf(l);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(double d, String indent) {
      return String.valueOf(d);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(float f, String indent) {
      return String.valueOf(f);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(ArrayList a, String indent) {
      StringBuffer buffer= new StringBuffer();
      
      buffer.append("array(\n");
      for (int i= 0; i < a.size(); i++) {
        buffer.append(indent);
        buffer.append(i);
        buffer.append(" => ");
        buffer.append(Exporter.export(a.get(i)));
      }
      buffer.append("\n");
      
      return buffer.toString();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static String export(Object o, String indent) {
      if (o == null) {
        return "NULL";
      }

      StringBuffer buffer= new StringBuffer();
      Class class= o.getClass();
      Field[] fields= class.getFields();
      Object value;

      buffer.append("class ");
      buffer.append(class.getName());
      buffer.append(" {\n");
      
      for (int i= 0; i < fields.length; i++) {
        buffer.append(indent);
        buffer.append(fields[i]);
        buffer.append("= ");
          
        try {
          value= fields[i].get(o);
        } catch (IllegalAccessException e) {
          value= null;
        }
        
        buffer.append(Exporter.export(value, indent+ "  "));
      }

      buffer.append('\n');
      buffer.append(indent);
      buffer.append('}');
      
      return buffer.toString();
    }
  }
