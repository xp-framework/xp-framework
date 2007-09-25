package net.xp_framework.cmd;

import java.util.Arrays;
import java.util.List;

public class ParamString {
    public List<String> list;
    public int count;

    /**
     * Constructor
     *
     */
    public ParamString(String[] args) {
        this.list= Arrays.asList(args);
        this.count= args.length;
    }
 
    /**
     * Private helper function that iterates through the parameter array
     * 
     * @param   longOption long parameter (w/o --)
     * @param   shortOption Short parameter (w/o -)
     * @return  position on which the parameter is placed or -1 if nonexistant
     */ 
    protected int find(String longOption, char shortOption) {
        for (int i= 0; i < this.count; i++) {
            String element= this.list.get(i);
        
            // Short notation (e.g. -f value)
            if (element.equals("-" + shortOption)) return i + 1;
            
            // Long notation (e.g. --help, without a value)
            if (element.equals("--" + longOption)) return i;

            // Long notation (e.g. --file=*.txt)
            if (element.substring(0, Math.min(element.length(), longOption.length() + 3)) .equals("--" + longOption + "=")) return i;
        }
        
        return -1;
    }
    
    /**
     * Private helper function that iterates through the parameter array. 
     * Calls find(String, char) with the second arg set to the first char 
     * of the long param.
     * 
     * @param   longOption long parameter (w/o --)
     * @return  position on which the parameter is placed or -1 if nonexistant
     */ 
    protected int find(String longOption) {
        return this.find(longOption, longOption.charAt(0));
    }   
    
    /**
     * Checks whether a parameter is set
     * 
     * @param   longOption long parameter (w/o --)
     * @param   shortOption Short parameter (w/o -)
     * @return  TRUE if the given parameter was set.
     */  
    public boolean exists(String longOption, char shortOption) {
        return this.find(longOption, shortOption) != -1;
    }

    /**
     * Checks whether a parameter is set. Calls exists(String, char) with
     * the second arg set to the first char of the long param.
     * 
     * @param   longOption long parameter (w/o --)
     * @return  TRUE if the given parameter was set.
     */  
    public boolean exists(String longOption) {
        return this.exists(longOption, longOption.charAt(0));
    }

    /**
     * Checks whether a positional parameter is set.
     * 
     * @param   position offset, starting at zero
     * @return  TRUE if the given parameter was set.
     */  
    public boolean exists(int position) {
        return (0 <= position && position < this.count);
    }

    /**
     * Retrieve the value of a given parameter
     *
     * Examples:
     * <code>
     *   ParamString p= new ParamString();
     *   if (p.exists("help", '?')) {
     *      System.out.printf("Usage: %s %s --force-check [--pattern={pattern}]\n", XXX, p.value(0));
     *      System.exit();
     *   }
     * 
     *   boolean force= p.exists("force-check", 'f');
     *   String pattern p.value("pattern", 'p', ".*");
     * 
     *   // ...
     * </code>
     * 
     * @param   longOption long parameter (w/o --)
     */ 
    public String value(String longOption) throws IllegalArgumentException {
        return this.value(longOption, longOption.charAt(0)); 
    }
    
    /**
     * Retrieve the value of a given parameter)
     *
     * @param   longOption long parameter (w/o --)
     * @param   shortOption Short parameter (w/o -)
     */ 
    public String value(String longOption, char shortOption) {
        int pos= this.find(longOption, shortOption);
        if (-1 == pos) {
            throw new IllegalArgumentException("Parameter --" + longOption + " does not exist");
        }
        
        // Default usage (eg.: '--with-foo=bar')
        String element= this.list.get(pos);
        if (element.substring(0, longOption.length()+ 2).equals("--" + longOption)) {

            // Usage with value (eg.: '--with-foo=bar')
            if ('=' == element.charAt(longOption.length()+ 2)) {
                return element.substring(longOption.length()+ 3, element.length());
            }

            // Usage as switch (eg.: '--enable-foo')
            return null;
        }

        // Usage in short (eg.: '-v' or '-f /foo/bar')
        // If the found element is a new parameter, the searched one is used as
        // flag, so just return TRUE, otherwise return the value.
        return element;
    }

    /**
     * Checks whether a positional parameter is set.
     * 
     * @param   position offset, starting at zero
     * @return  String value
     */  
    public String value(int position) throws IllegalArgumentException {
        if (0 <= position && position < this.count) {
            return this.list.get(position);
        }
        throw new IllegalArgumentException ("Parameter #"+ position + " does not exist");
    }
}
