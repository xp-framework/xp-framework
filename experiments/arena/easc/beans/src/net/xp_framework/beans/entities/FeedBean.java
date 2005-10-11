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
 * @ejb.transaction
 *      type="Supports"
 * @ejb.finder
 *      signature="Feed findByPrimaryKey(java.lang.Long primaryKey)"
 * @ejb.finder
 *      signature="Collection findAll()"
 *      query="SELECT OBJECT(o) FROM Feed AS o WHERE o.bz_id = 500
 * @jboss.create-table "false"
 * @jboss.remove-table "false"
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
     * Public no-arg constructor
     *
     * @access  public
     */
    public FeedBean() {}

    /**
     * EJB create method
     *
     * @access  public
     * @param   java.lang.Long feed_id
     * @return  java.lang.Long
     */
    public Long ejbCreate(Long feed_id) throws CreateException {
        this.setFeed_id(feed_id);
        return null;
    }

    /**
     * EJB post-create method
     *
     * @access  public
     * @param   java.lang.Long feed_id
     */
    public void ejbPostCreate(Long feed_id) {
    }

    /**
     * EJB activate method
     *
     * @access  public
     */
    public void ejbActivate() {
    }

    /**
     * EJB passivate method
     *
     * @access  public
     */
    public void ejbPassivate() {
    }

    /**
     * EJB load method
     *
     * @access  public
     */
    public void ejbLoad() {
    }

    /**
     * EJB store method
     *
     * @access  public
     */
    public void ejbStore() {
    }

    /**
     * EJB remove method
     *
     * @access  public
     */
    public void ejbRemove() {
    }

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
        context= null;
    }
}
