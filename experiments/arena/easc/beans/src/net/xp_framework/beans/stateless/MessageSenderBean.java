/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.stateless;

import javax.ejb.EJBException;
import javax.ejb.SessionBean;
import javax.ejb.SessionContext;
import java.rmi.RemoteException;
import java.util.HashMap;
import java.util.Iterator;
import javax.jms.*;
import javax.naming.InitialContext;

/**
 * JMS bridge demonstration
 *
 * @ejb.bean
 *      name="MessageSender"
 *      type="Stateless"
 *      view-type="both"
 *      local-jndi-name="xp/demo/MessageSender"
 *      jndi-name="xp/demo/MessageSender"
 *      display-name="Stateless to JMS-Bridge bean"
 */
public class MessageSenderBean implements SessionBean {

    /**
     * Send mesage
     *
     * @ejb.interface-method view-type = "both"
     * @access  public
     * @param   String queueName
     * @param   String text
     */
    public void sendTextMessage(String queueName, String text) throws Exception { 
      InitialContext ctx= new InitialContext();

      QueueConnectionFactory queueConnectionFactory = (QueueConnectionFactory)ctx.lookup("ConnectionFactory");
      Queue queue= (Queue) ctx.lookup(queueName);
      QueueConnection queueConnection= queueConnectionFactory.createQueueConnection();
      QueueSession queueSession= queueConnection.createQueueSession(false, Session.AUTO_ACKNOWLEDGE);
      QueueSender queueSender= queueSession.createSender(queue);
      TextMessage message= queueSession.createTextMessage();
      
      message.setText(text);
      queueSender.send(message);
    }

    /**
     * Send a map mesage
     *
     * @ejb.interface-method view-type = "both"
     * @access  public
     * @param   String queueName
     * @param   java.util.HashMap values
     */
    public void sendMapMessage(String queueName, HashMap values) throws Exception { 
      InitialContext ctx= new InitialContext();

      QueueConnectionFactory queueConnectionFactory = (QueueConnectionFactory)ctx.lookup("ConnectionFactory");
      Queue queue= (Queue) ctx.lookup(queueName);
      QueueConnection queueConnection= queueConnectionFactory.createQueueConnection();
      QueueSession queueSession= queueConnection.createQueueSession(false, Session.AUTO_ACKNOWLEDGE);
      QueueSender queueSender= queueSession.createSender(queue);
      MapMessage message= queueSession.createMapMessage();
      
      for (Iterator i= values.keySet().iterator(); i.hasNext(); ) {
          Object key= i.next(); 
          message.setObject(key.toString(), values.get(key));
      }
      
      queueSender.send(message);
    }
    
    /**
     * Activate method
     *
     * @access  public
     */
    public void ejbActivate() throws EJBException, RemoteException { }

    /**
     * Passivate method
     *
     * @access  public
     */
    public void ejbPassivate() throws EJBException, RemoteException { }

    /**
     * Remove method
     *
     * @access  public
     */
    public void ejbRemove() throws EJBException, RemoteException { }

    /**
     * Session context injection method
     *
     * @access  public
     */
    public void setSessionContext(SessionContext sessionContext) throws EJBException, RemoteException { }
}
