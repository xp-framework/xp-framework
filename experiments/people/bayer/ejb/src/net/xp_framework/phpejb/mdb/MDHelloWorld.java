package net.xp_framework.phpejb.mdb;

import javax.ejb.MessageDriven;
import javax.ejb.ActivationConfigProperty;
import javax.jms.Message;
import javax.jms.TextMessage;
import javax.jms.MessageListener;
import javax.script.*;



@MessageDriven(activationConfig = {
  @ActivationConfigProperty(propertyName="destinationType", propertyValue="javax.jms.Queue"),
  @ActivationConfigProperty(propertyName="destination", propertyValue="queue/mdb")
})
public class MDHelloWorld implements MessageListener {

    
    public void onMessage (Message msg) {
        try {
            TextMessage tmsg = TextMessage.class.cast(msg);

            ScriptEngineManager mgr = new ScriptEngineManager();
            ScriptEngine eng = mgr.getEngineByName("turpitude");
            if (null == eng)
                return;

            Compilable comp = (Compilable)eng;
            CompiledScript script = comp.compile(getSource());
            Invocable inv = (Invocable)script;
            Object phpobj = inv.invokeFunction("getBean");
            Object retval = inv.invokeMethod(phpobj, "onMessage", tmsg.getText());
            System.out.println(retval);
        } catch(Throwable e) {
            e.printStackTrace();
        }
    }

    private static String getSource() {
        StringBuffer src = new StringBuffer();
        src.append("<?php");
        src.append("class MDHelloWorld {");
        src.append("  function onMessage($s) {");
        src.append("    return 'The PHP-implementation received a message from '.$s;");
        src.append("  }");
        src.append("}");
        src.append("function getBean() {");
        src.append("  return new MDHelloWorld();");
        src.append("}");
        src.append("?>");
        return src.toString();
    }

}

