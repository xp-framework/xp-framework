package net.xp_framework.ejbtest.list;

import javax.ejb.Stateless;
import javax.script.*;
import java.util.List;
import java.util.ArrayList;
import java.util.ListIterator;

@Stateless
public class EngineListBean implements EngineList {
    
    public List<String> getList() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        List<ScriptEngineFactory> lst = mgr.getEngineFactories();
        ListIterator<ScriptEngineFactory> it = lst.listIterator();

        List<String> retval = new ArrayList<String>();
        while (it.hasNext()) {
            retval.add(it.next().getEngineName());
        }

        return retval;
    }
}

