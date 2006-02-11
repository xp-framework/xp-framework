/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import net.xp_framework.easc.protocol.standard.ServerHandler;
import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.reflect.ClassWrapper;

import java.util.ArrayList;
import java.lang.reflect.Field;

public class ReflectionServerHandler extends ServerHandler {
    
    /**
     * Setup this handler
     *
     * @access  public
     * @param   net.xp_framework.easc.server.ServerContext ctx
     */
    public void setup(final ServerContext ctx) {
        Serializer.registerMapping(ClassWrapper.class, new Invokeable<String, ClassWrapper>() {
            public String invoke(ClassWrapper w, Object arg) throws Exception {
                StringBuffer buffer= new StringBuffer();
                String name= ClassWrapper.class.getName();

                // Object declaration
                buffer.append("O:").append(name.length()).append(":\"").append(name).append("\":2:{");

                // Referenced class' name
                buffer.append("s:9:\"className\";").append(Serializer.representationOf(w.referencedClass.getName()));

                // Fields
                ArrayList<Field> fields= Serializer.classFields(w.referencedClass);
                buffer.append("s:6:\"fields\";a:").append(fields.size()).append(":{");
                for (Field f : fields) { 
                    buffer.append("s:");
                    buffer.append(f.getName().length());
                    buffer.append(":\"");
                    buffer.append(f.getName());
                    buffer.append("\";");

                    buffer.append(Serializer.representationOf(f.getType()));
                }
                
                buffer.append("}}");        
                return buffer.toString();
            }
        });
    }
}
