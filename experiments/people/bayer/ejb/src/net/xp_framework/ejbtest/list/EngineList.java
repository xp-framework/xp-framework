package net.xp_framework.ejbtest.list;

import javax.ejb.Remote;
import java.util.List;

@Remote
public interface EngineList {

    public List<String> getList();

}

