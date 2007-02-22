package net.xp_framework.phpejb.list;

import javax.ejb.Remote;
import java.util.List;

@Remote
public interface EngineList {

    public List<String> getList();

}

