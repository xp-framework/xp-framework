package net.xp_framework.turpitude;

import java.util.HashMap;
import java.util.Set;
import java.util.Iterator;

/**
 * A PHP object. Remember, this is not a reference or a representation or anything.
 * A <code>PHPObject</code> is just a (very crude) copy of a PHP object, changes 
 * <b>will not</b> have any effects whatsoever on the executed script.
 * Especially passing a <code>PHPObject</code> back to a script will not work as
 * the user might expect.
 */
public class PHPObject {

    private String ClassName = "";
    private HashMap<String, Object> Properties = new HashMap<String, Object>();
    private transient java.nio.ByteBuffer ZValptr;

    /**
     * default constructor.
     *
     * @param  classname
     *          The name of the class this PHPObject was created from
     */
    public PHPObject(String classname) {
        setClassName(classname);
    }

    public String getClassName() {
        return ClassName;
    }

    public void setClassName(String cn) {
        ClassName = cn;
    }

    public void setProperty(String key, Object val) {
        Properties.put(key, val);
    }

    public HashMap getProperties() {
        return Properties;
    }

    public void dump() {
        System.out.println("PHPObject, class = " + getClassName());
        Set<String> keys = Properties.keySet();
        Iterator<String> it = keys.iterator();
        while (it.hasNext()) {
            String k = it.next();
            Object v = Properties.get(k);
            if (v == null)
                System.out.println("  Property " + k + " = NULL");
            else
                System.out.println("  Property " + k + " = " + v.getClass() + " (" + v + ")");
        }
        System.out.println("<<<===");
       
    }


}
