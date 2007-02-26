package net.xp_framework.phpejb.slsb;

import javax.ejb.Remote;
import java.util.List;
import javax.script.ScriptException;

@Remote
public interface SLHelloWorld {

    public String sayHello(String s) throws ScriptException;

}

