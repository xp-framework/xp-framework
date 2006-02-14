package net.xp_framework.beans.mdb;

import javax.ejb.EJBException;
import javax.ejb.MessageDrivenBean;
import javax.ejb.MessageDrivenContext;
import javax.jms.Message;
import javax.jms.MessageListener;

/**
 * @ejb.bean
 *      name="MessageProcessor"
 *      display-name="Message interface"
 *      acknowledge-mode="Auto-acknowledge"
 *      destination-type="javax.jms.Queue"
 *      subscription-durability="NonDurable"
 *      transaction-type="Container"
 *
 * @jboss.destination-jndi-name
 *      name="queue/MessageQueue"
 */
public class MessageBean implements MessageDrivenBean, MessageListener {
    private MessageDrivenContext messageContext = null;

    public void onMessage(Message message) {
        System.out.println("onMessage() called " + message);
    }

    public void setMessageDrivenContext(MessageDrivenContext messageContext) throws EJBException {
        this.messageContext = messageContext;
    }

    public void ejbCreate() {
    }

    public void ejbRemove() {
        messageContext = null;
    }
}
