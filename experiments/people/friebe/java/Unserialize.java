import java.io.*;
import java.lang.reflect.*;
import java.util.Date;

  class techauftrag {
    public Date lastchange     = null;
    public int techauftrag_id  = 0;
    public int kunde_id        = 0;
    public int bz_id           = 0;
    public int ext_vertragnr   = 0;
    public int masteruser      = 0;
    public int location_id     = 0;
    public String initpassword = "";
    public String changedby    = "";
    public String bemerkung    = "";
    
    public String toString() {
      int i;
      String ret= "";
      Field[] fields= this.getClass().getFields();
      Object value;
      
      for (i= 0; i < fields.length; i++) {
        try {
          value= fields[i].get(this);
        } catch (IllegalAccessException e) {
          value= "(???)";
        }
        ret += "  " + fields[i] + " = " + value + "\n";
      }
      return getClass().getName() + " {\n" + ret + "}";
    }
  }

  class date {
  }

  class UnserializeException extends Exception {
    public Throwable cause= null;

    public UnserializeException(String msg, Throwable c) {
      super(msg);
      cause= c;
    }

    public UnserializeException(Throwable c) {
      super(c.getMessage());
      cause= c;
    }
  }
  
  class StringPortion {
    public String string;
    public int end;
    
    public StringPortion(String s, int b, int l) {
      end= b + l;
      string= s.substring(b, end);
      System.out.println("\n> Portion from " + b + " - " + end + " = '" + string + "'");
    }
    
    public String getString() {
      return string;
    }
    
    public int getEnd() {
      return end;
    }
  }

  public class Unserialize {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    private static StringPortion getStringAt(String buffer, int position) {
      String size= buffer.substring(position + 2, buffer.indexOf(':', position + 2));
      return new StringPortion(buffer, position + 4 + size.length(), Integer.parseInt(size));
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    private static Object objectFor(String classname) throws UnserializeException {
      Object o;

      try {
        o= Class.forName(classname).newInstance();
      } catch (ClassNotFoundException e) {
        throw new UnserializeException(e);
      } catch (InstantiationException e) {
        throw new UnserializeException(e);
      } catch (IllegalAccessException e) {
        throw new UnserializeException(e);
      }  

      System.out.print("--> Object '" + classname + "' ==> '" + o.getClass().getName() + "'");
      return o;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    private static Field fieldFor(Class c, String fieldname) throws UnserializeException {
      Field f;

      try {
        f= c.getField(fieldname);
      } catch (NoSuchFieldException e) {
        throw new UnserializeException(e);
      }

      System.out.print("--> Field '" + fieldname + "' ==> '" + f + "'");
      return f;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    private static void setFieldValue(Field f, Object o, Object value) throws UnserializeException {
      try {
        f.set(o, value);
      } catch(IllegalAccessException e) {
        throw new UnserializeException(e);
      }

      String type= (value == null) ? "NULL" : value.getClass().getName();
      System.out.print("--> Field '" + f + "' ==> (" + type + ")" + value);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    private static void setFieldValue(Field f, Object o, int value) throws UnserializeException {
      try {
        f.setInt(o, value);
      } catch(IllegalAccessException e) {
        throw new UnserializeException(e);
      }

      System.out.print("--> Field '" + f + "' ==> (int)" + value);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public static Object unserialize(String buffer) throws UnserializeException {
      Object object= null;
      int i= 0;
      int size;
      String len;
      Field key= null;

      while (i < buffer.length()) {
        System.out.print(key + " #" + i + " {'" + buffer.charAt(i) + "'}: ");
        switch (buffer.charAt(i)) {
          case 'O':    // O:11:"techauftrag":10:{
            StringPortion classname= getStringAt(buffer, i);
            object= objectFor(classname.getString());
            i= buffer.indexOf('{', classname.getEnd());
            break;

          case 's':   // s:10:"lastchange";
            StringPortion content= getStringAt(buffer, i);
            
            if (key == null) {
              key= fieldFor(object.getClass(), content.getString());
            } else {
              setFieldValue(key, object, content.getString());
              key= null;
            }
            i= content.getEnd() + 1;
            break;
            
          case 'i':   // i:6960459;
            String value= buffer.substring(i + 2, buffer.indexOf(';', i + 2));
            setFieldValue(key, object, Integer.parseInt(value));
            key= null;
            i += value.length() + 2;
            break;
          
          case 'N':
            setFieldValue(key, object, null);
            key= null;
            i++;
            break;

          default:
            System.out.print("--> *** Unrecognized >"+ buffer.charAt(i)+ "< ***");
        }
        i++;
        System.out.println();
      }
      return object;
    }

    public static void main(String[] args) throws IOException {
      BufferedReader r= new BufferedReader(new FileReader(args[0]));
      String buffer= r.readLine();

      System.out.println(buffer);

      try {
        System.out.println(unserialize(buffer).toString());
      } catch (UnserializeException e) {
        e.printStackTrace();
      }
    }
  }
