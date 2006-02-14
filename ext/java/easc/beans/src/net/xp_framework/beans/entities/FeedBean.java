/* This class is part of the XP framework's EAS connectivity 
 *
 * $Id$
 */

package net.xp_framework.beans.entities;

import java.sql.Timestamp;
import java.util.Collection;
import javax.ejb.EJBException;
import javax.ejb.CreateException;
import javax.ejb.EntityBean;
import javax.ejb.EntityContext;
import java.rmi.RemoteException;

import net.xp_framework.beans.entities.FeedValue;

/**
 * Planet XP Feed bean
 *
 * @ejb.bean
 *      name="Feed"
 *      type="CMP"
 *      cmp-version="2.x"
 *      view-type="both"
 *      local-jndi-name="xp/planet/Feed"
 *      jndi-name="xp/planet/Feed"
 *      display-name="Planet XP Feed"
 *      primkey-field="feed_id"
 * @ejb.persistence
 *      table-name="feed"
 * @ejb.pk
 *      class="java.lang.Long"
 *      generate="false"
 * @ejb.value-object
 *      name="Feed"
 *      match="*"
 * @ejb.transaction
 *      type="Supports"
 * @ejb.finder
 *      signature="Feed findByPrimaryKey(java.lang.Long primaryKey)"
 * @ejb.finder
 *      signature="Collection findActive()"
 *      query="SELECT OBJECT(o) FROM Feed AS o WHERE o.bz_id = 500"
 * @ejb.finder
 *      signature="Collection findAll()"
 *      query="SELECT OBJECT(o) FROM Feed AS o"
 * @jboss.entity-command name = "mysql-get-generated-keys"
 * @jboss.create-table "false"
 * @jboss.remove-table "false"
 * @jboss.persistence datasource="java:/jdbc/XPSyndicateDS" datasource-mapping="mySQL"
 */
public abstract class FeedBean implements EntityBean {
    transient private EntityContext context= null;

    /**
     * Gets feed's id (primary key)
     *
     * @ejb.pk-field
     * @ejb.interface-method
     * @ejb.persistence column-name="feed_id"
     * @access  public
     * @return  java.lang.Long
     */
    public abstract Long getFeed_id();

    /**
     * Sets the feed's id
     *
     * @ejb.interface-method
     * @access  public
     * @param   java.lang.Long feed_id
     */
    public abstract void setFeed_id(Long feed_id);

    /**
     * Sets feed's url
     *
     * @ejb.interface-method
     * @ejb.persistence column-name="url"
     * @access  public
     * @return  java.lang.String
     */
    public abstract String getUrl();

    /**
     * Gets feed's url
     *
     * @ejb.interface-method
     * @access  public
     * @param   java.lang.String url
     */
    public abstract void setUrl(String url);

    /**
     * Sets feed's title
     *
     * @ejb.interface-method
     * @ejb.persistence column-name="title"
     * @access  public
     * @return  java.lang.String
     */
    public abstract String getTitle();

    /**
     * Gets feed's title
     *
     * @ejb.interface-method
     * @access  public
     * @param   java.lang.String title
     */
    public abstract void setTitle(String title);

    /**
     * Sets feed's lastchange
     *
     * @ejb.interface-method
     * @ejb.persistence column-name="lastchange"
     * @access  public
     * @return  java.lang.String
     */
    public abstract Timestamp getLastchange();

    /**
     * Gets feed's lastchange
     *
     * @ejb.interface-method
     * @access  public
     * @param   java.lang.String lastchange
     */
    public abstract void setLastchange(Timestamp lastchange);

    /**
     * Gets feed's bz_id (status: 500 = active, 30000 = deactivated)
     *
     * @ejb.interface-method
     * @ejb.persistence column-name="bz_id"
     * @access  public
     * @return  java.lang.Long
     */
    public abstract Long getBz_id();

    /**
     * Sets the feed's bz_id
     *
     * @ejb.interface-method
     * @access  public
     * @param   java.lang.Long bz_id
     */
    public abstract void setBz_id(Long bz_id);

    /**
     * Gets value object
     *
     * @ejb.interface-method
     * @access  public
     * @return  net.xp_framework.beans.entities.FeedValue value
     */
    public abstract FeedValue getFeedValue();

    /**
     * Sets value object
     *
     * @ejb.interface-method
     * @access  public
     * @param   net.xp_framework.beans.entities.FeedValue value
     */
    public abstract void setFeedValue(FeedValue value);
    

    /**
     * Set entity context
     *
     * @access  public
     * @param   javax.ejb.EntityContext context
     */
    public void setEntityContext(EntityContext context) throws EJBException, RemoteException {
        this.context = context;
    }

    /**
     * Unset entity context
     *
     * @access  public
     */
    public void unsetEntityContext() throws EJBException, RemoteException {
        this.context= null;
    }
    
    /**
     * EJB create method
     *
     * You might be wondering why here, even though the return type of the ejbCreate() 
     * method should be an entity bean's primary key class instance, a null value is
     * returned. This is not a programming mistake -- in fact, in the case of container-
     * managed persistence, the container knows this and actually ignores the null 
     * value.
     * 
     * @ejb.create-method
     * @access  public
     * @param   net.xp_framework.beans.entities.FeedValue data
     * @return  java.lang.Object
     */
    public Long ejbCreate(FeedValue data) throws CreateException {
        this.setFeedValue(data);
        return null;
    }

    /**
     * EJB post-create method
     * 
     * @access  public
     * @param   net.xp_framework.beans.entities.FeedValue value
     */
    public void ejbPostCreate(FeedValue value) throws CreateException {
    }
}
