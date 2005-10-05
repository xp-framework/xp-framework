/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop;

import java.io.InputStream;

public class JarTemplateLoader extends TemplateLoader {

    public String nameFor(String abs) {
        return "/templates/" + super.nameFor(abs) + ".fo";
    }

    public String templateFor(String abs) throws Exception {
        InputStream stream= this.getClass().getResourceAsStream(this.nameFor(abs));
        
        // Check if template could be found...
        if (null == stream) {
            throw (new IllegalArgumentException("No such template: '" + abs + "'"));
        }
        
        // Create a StringBuffer to fill it
        StringBuffer ret= new StringBuffer(stream.available());
        
        byte buf[]= new byte[2048];
        int read= 0;
        do {
          read= stream.read(buf);
          if (0 < read) ret.append(new String(buf, 0, read));
        } while (read > -1);
        
        return ret.toString();
    }
}
