/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
import com.sun.javadoc.*;
import java.io.PrintStream;
import java.io.FileOutputStream;
import java.io.File;
import java.io.IOException;
import java.util.StringTokenizer;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

  /**
   * Generates an XML representation of EJB-annotated classes
   *
   * @purpose  Transformer
   */
  public class EjbToXMLDoclet {

    /**
     * Utility method
     *
     * @model   static
     * @access  protected
     * @param   String tag
     * @return  HashMap
     */
    protected static HashMap parseKeyValuePairsFrom(String tag) {
      StringTokenizer st = new StringTokenizer(tag, "= \r\n\t");
      HashMap pairs = new HashMap();
      
      // Parse key/value pairs from @ejb.bean tag
      while (st.hasMoreTokens()) {
        String key = st.nextToken();
        
        String value = st.nextToken();
        if (!value.endsWith("\"")) {
          while (st.hasMoreTokens()) {
            value += st.nextToken("\"");
          }
          value += "\"";
        }
        
        pairs.put(key, value.substring(1, value.length()- 1));
      }
      
      return pairs;
    }

    /**
     * Processes a single class
     *
     * @model   static
     * @access  public
     * @param   ClassDoc classdoc a single class' documentation
     * @param   HashMap options key/value pairs from ejb.bean tag
     * @param   PrintStream out the stream the XML is printed to
     * @return  boolean
     */
    public static boolean processClass(ClassDoc classdoc, HashMap options, PrintStream out) {
      out.print("<interface\n");
      for (Iterator i= options.entrySet().iterator(); i.hasNext(); ) {
        Map.Entry e = (Map.Entry) i.next();
        
        out.print(" " + e.getKey() + "=\"" + e.getValue() + "\"\n");
      }
      out.println(">");

      // Go through all the methods
      MethodDoc[] methods = classdoc.methods();
      for (int i= 0; i < methods.length; i++) {
        Tag[] tags = methods[i].tags("@ejb.interface-method");
        if (0 == tags.length) continue;

        // Create method details:
        out.println("  <method name=\""+ methods[i].name() + "\">");
        out.println("    <comment><![CDATA[" + methods[i].commentText() + "]]></comment>");
        out.println("    <return type=\"" + methods[i].returnType().qualifiedTypeName() + "\"/>");

        // Add parameters
        Parameter[] parameters = methods[i].parameters();
        if (0 == parameters.length) {
          out.println("    <parameters/>");
        } else {
          out.println("    <parameters>");
          for (int j= 0; j < parameters.length; j++) {
            out.println(
              "      <parameter name=\"" + 
              parameters[j].name() + 
              "\" type=\"" + parameters[j].typeName() + "\"" +
              " />"
            );
          }
          out.println("    </parameters>");
        }
        
        // Add throws
        ClassDoc[] thrownExceptions = methods[i].thrownExceptions();
        if (0 == thrownExceptions.length) {
          out.println("    <throws/>");
        } else {
          out.println("    <throws>");
          for (int j= 0; j < thrownExceptions.length; j++) {
            out.println(
              "      <exception name=\"" + 
              thrownExceptions[j].qualifiedName() + 
              "\"/>"
            );
          }
          out.println("    </throws>");
        }

        out.println("  </method>");
      }

      out.println("</interface>");
      return true;
    }

    /**
     * Doclet method
     *
     * @model   static
     * @access  public
     * @param   RootDoc root
     * @return  boolean
     */
    public static boolean start(RootDoc root) throws IOException {
      HashMap options = parseKeyValuePairsFrom(root.classes()[0].tags("@ejb.bean")[0].text());
      String name = (String)options.get("name");

      System.out.println("@@=" + name);
      return processClass(
        root.classes()[0], 
        options,
        new PrintStream(new FileOutputStream(new File(name+ ".gen.xml")))
      );
    }
  }
