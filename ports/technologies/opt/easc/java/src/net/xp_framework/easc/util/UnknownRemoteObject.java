package net.xp_framework.easc.util;

import java.util.HashMap;
import java.util.Map;

public class UnknownRemoteObject {
    protected String className;
    public HashMap<String, Object> members = new HashMap<String, Object>();
    
    public UnknownRemoteObject(String className) {
        this.className = className;
    }
    
    @Override public String toString() {
        StringBuilder b = new StringBuilder();
        b.append(this.getClass().getName()).append("<").append(this.className).append(">@{\n");
        for (Map.Entry<String, Object> entry : members.entrySet()) {
            b.append("  ").append(entry.getKey()).append(" => ");
            b.append(entry.getValue().toString().replace("\n", "\n  ")).append("\n");
        }
        return b.append("}").toString();
    }
}
