package net.xp_framework.phpejb.sfsb;

import javax.ejb.Remote;
import javax.script.ScriptException;

@Remote
public interface SFHelloWorld {

    public void setName(String s) throws ScriptException;
    public String sayHello() throws ScriptException;

}

