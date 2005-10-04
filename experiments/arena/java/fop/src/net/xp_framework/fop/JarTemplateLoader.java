/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop;

import java.io.ByteArrayInputStream;

public class JarTemplateLoader extends TemplateLoader {

    public String templateFor(String abs) {
        ByteArrayInputStream stream= 
            (ByteArrayInputStream)this.getClass().getClassLoader().getResourceAsStream(this.nameFor(abs));
        
        byte buf[]= new byte[stream.available()];
        stream.read(buf, stream.available(), 0);

        return new String(buf);
    }
}
