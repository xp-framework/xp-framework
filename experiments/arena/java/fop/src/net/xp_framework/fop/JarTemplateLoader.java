/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.fop;

import java.io.InputStream;

public class JarTemplateLoader extends TemplateLoader {

    public String nameFor(String abs) {
        String name= super.nameFor(abs);
        String template= "templates/" + name + ".fo";
        System.out.println("Loading template from '" + template + "'");
        return template;
    }

    public String templateFor(String abs) throws Exception {
        InputStream stream= 
            this.getClass().getClassLoader().getResourceAsStream(this.nameFor(abs));
        
        byte buf[]= new byte[stream.available()];
        stream.read(buf, stream.available(), 0);
        
        System.out.println("Got content for template: " + new String(buf));

        return new String(buf);
    }
}
