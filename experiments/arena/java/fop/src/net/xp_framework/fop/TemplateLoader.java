/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop;

public class TemplateLoader {
    
    public String nameFor(String abs) {
        
        // Convert to name
        String name= abs.replace(".", "/");
        return name;
    }
    
    public String templateFor(String abs) throws Exception { return null; }
}
