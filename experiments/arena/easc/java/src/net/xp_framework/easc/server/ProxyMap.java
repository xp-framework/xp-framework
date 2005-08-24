/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import java.util.HashMap;

public class ProxyMap {
    private class NamedObject {
        public String name;
        public Object object;

        public NamedObject(String name, Object object) {
            this.name= name;
            this.object= object;
        }
    }
    private static long identifier = 0L;

    private HashMap<Long, NamedObject> map= new HashMap<Long, NamedObject>();

    public long put(String name, Object o) {
        identifier++;
        this.map.put(identifier, new NamedObject(name, o));
        return identifier;
    }
    
    public Object getObject(long id) {
        return this.map.get(id).object;
    }
}
