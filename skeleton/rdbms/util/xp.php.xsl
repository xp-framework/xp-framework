<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" omit-xml-declaration="yes"/>
  
  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>
  
  <xsl:template name="prettyname">
    <xsl:param name="string"/>
	
	<xsl:value-of select="concat(
	  translate(substring($string, 1, 1), $lcletters, $ucletters),
	  translate(substring($string, 2), $ucletters, $lcletters)
	)"/>
  </xsl:template>	
  
  <xsl:template match="/">
    <xsl:text>&lt;?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.ConnectionManager');
 </xsl:text>
      <xsl:apply-templates/>
	<xsl:text>?></xsl:text>
  </xsl:template>
  
  <xsl:template match="table">
    <xsl:variable name="primary_key_unique" select="index[@primary= 'true' and @unique= 'true']/key/text()"/>
    <xsl:text>/**
   * Class wrapper for table </xsl:text><xsl:value-of select="@name"/><xsl:text>
   *
   * (Auto-generated on </xsl:text><xsl:value-of select="concat(
     ../@created_at, 
	 ' by ', 
	 ../@created_by
   )"/><xsl:text>)
   * @purpose  Datasource accessor
   */
  class </xsl:text>
	<xsl:call-template name="prettyname">
	  <xsl:with-param name="string" select="@name"/>
	</xsl:call-template>
	<xsl:text> extends Object {&#10;    var&#10;</xsl:text>
	
	<!-- Attributes -->
	<xsl:for-each select="attribute">
	  <xsl:value-of select="concat('      $', @name)"/>
	  <xsl:choose>
	    <xsl:when test="@typename= 'int'">= 0</xsl:when>
		<xsl:when test="@typename= 'string'">= ''</xsl:when>
		<xsl:when test="@typename= 'float'">= 0.0</xsl:when>
		<xsl:when test="@typename= 'bool'">= FALSE</xsl:when>
		<xsl:when test="@typename= 'util.Date'">= NULL</xsl:when>
      </xsl:choose>
	  <xsl:if test="position() != last()">,&#10;</xsl:if>
	</xsl:for-each>
	<xsl:text>;&#10;</xsl:text>
	
	<!-- Create a static method for indexes -->
	<xsl:for-each select="index[@name != '']">
	  <xsl:text>
    /**
     * Gets an instance of this object by index "</xsl:text><xsl:value-of select="@name"/><xsl:text>"
     *
     * @access  static</xsl:text>
	  <xsl:for-each select="key">
	    <xsl:variable name="key" select="text()"/>
		<xsl:text>
     * @param   </xsl:text>
	    <xsl:value-of select="concat(../../attribute[@name= $key]/@typename, ' ', $key)"/>
	  </xsl:for-each>
	  <xsl:text>
     * @return  &amp;</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="../@name"/>
	  </xsl:call-template>
      <xsl:if test="not(@unique= 'true')">[]</xsl:if>
	  <xsl:text> object
     * @throws  SQLException in case an error occurs
     * @throws  IllegalAccessException in case there is no suitable database connection available
     */
    function &amp;getBy</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="key/text()"/>
	  </xsl:call-template>
	  <xsl:text>(</xsl:text>
	  <xsl:for-each select="key">
	    <xsl:value-of select="concat('$', text())"/>
		<xsl:if test="position() != last()">, </xsl:if>
	  </xsl:for-each>
	  <xsl:text>) {
      $cm= &amp;ConnectionManager::getInstance();  
      if (FALSE === ($db= &amp;$cm->getByHost('***', 0))) {
        return throw(new IllegalAccessException('No connection available'));
      }

      try(); {
        $q= $db->query('
          select&#10;</xsl:text>
      <xsl:for-each select="../attribute">
	    <xsl:text>            </xsl:text>
	    <xsl:value-of select="@name"/>
		<xsl:if test="position() != last()">,&#10;</xsl:if>
	  </xsl:for-each>
	  <xsl:text>
          from
            </xsl:text><xsl:value-of select="../@name"/><xsl:text> 
          where
            </xsl:text>
	  <xsl:for-each select="key">
		<xsl:variable name="key" select="text()"/>
		<xsl:variable name="typename" select="../../attribute[@name= $key]/@typename"/>
		
		<xsl:value-of select="concat($key, ' = ')"/>
		<xsl:choose>
	      <xsl:when test="$typename= 'int'">%d</xsl:when>
		  <xsl:when test="$typename= 'string'">%s</xsl:when>
		  <xsl:when test="$typename= 'float'">%f</xsl:when>
		  <xsl:when test="$typename= 'bool'">%d</xsl:when>
		  <xsl:when test="$typename= 'util.Date'">%s</xsl:when>
		  <xsl:otherwise>= %c</xsl:otherwise>
    	</xsl:choose>
		<xsl:if test="position() != last()">&#10;              and </xsl:if>
	  </xsl:for-each>
  	  <xsl:text>
        ', </xsl:text>
	  <xsl:for-each select="key">
	    <xsl:value-of select="concat('$', text())"/>
		<xsl:if test="position() != last()">, </xsl:if>
	  </xsl:for-each>
	  <xsl:text>);&#10;</xsl:text>
        <xsl:choose>
          <xsl:when test="@unique = 'true'">
            <xsl:text>
        if ($r= $db-&gt;fetch($q)) $data= &amp;new </xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="../@name"/>
	  </xsl:call-template>
      <xsl:text>($r); else $data= NULL;</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>
        $data= array();
        while ($r= $db-&gt;fetch($q)) {
          $data[]= &amp;new </xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="../@name"/>
	  </xsl:call-template>
      <xsl:text>($r);
        }</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      <xsl:text>
      } if (catch('SQLException', $e)) {

        // more error handling TBD here?
        return throw($e);
      }

      return $data;
    }&#10;</xsl:text>
	</xsl:for-each>
	
	<!-- Create getters and setters -->
    <xsl:for-each select="attribute">
      <xsl:text>
    /**
     * Retreives </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @access  public
     * @return  </xsl:text><xsl:value-of select="@typename"/><xsl:text>
     */
    function get</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="@name"/>
	  </xsl:call-template>
      <xsl:text>() {
      return $this-></xsl:text><xsl:value-of select="@name"/><xsl:text>;
    }
      </xsl:text>
	  
      <xsl:text>
    /**
     * Sets </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @access  public
     * @param   </xsl:text><xsl:value-of select="concat(@typename, ' ', @name)"/><xsl:text>
     */
    function set</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="@name"/>
	  </xsl:call-template>
      <xsl:text>($</xsl:text><xsl:value-of select="@name"/><xsl:text>) {
      $this-></xsl:text><xsl:value-of select="concat(@name, '= $', @name)"/><xsl:text>;
    }
      </xsl:text>
	</xsl:for-each>

	<!-- Create update() method -->
	<xsl:text>
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  boolean success
     * @throws  SQLException in case an error occurs
     * @throws  IllegalAccessException in case there is no suitable database connection available
     */
    function update() {
      $cm= &amp;ConnectionManager::getInstance();  
      if (FALSE === ($db= &amp;$cm->getByHost('***', 0))) {
        return throw(new IllegalAccessException('No connection available'));
      }

      try(); {
        $db->update('
          </xsl:text><xsl:value-of select="@name"/><xsl:text> set&#10;</xsl:text>
      <xsl:for-each select="attribute[@identity = 'false']">
		<xsl:text>            </xsl:text>
		<xsl:value-of select="concat(@name, ' = ')"/>
		<xsl:choose>
	      <xsl:when test="@typename= 'int'">%d</xsl:when>
		  <xsl:when test="@typename= 'string'">%s</xsl:when>
		  <xsl:when test="@typename= 'float'">%f</xsl:when>
		  <xsl:when test="@typename= 'bool'">%d</xsl:when>
		  <xsl:when test="@typename= 'util.Date'">%s</xsl:when>
		  <xsl:otherwise>= %c</xsl:otherwise>
    	</xsl:choose>
		<xsl:if test="position() != last()">,&#10;</xsl:if>
	  </xsl:for-each>

      <xsl:text>
          where
            </xsl:text>
      <xsl:for-each select="attribute[@identity= 'true']">
	    <xsl:value-of select="concat(@name, ' = ')"/>
		<xsl:choose>
	      <xsl:when test="@typename= 'int'">%d</xsl:when>
		  <xsl:when test="@typename= 'string'">%s</xsl:when>
		  <xsl:when test="@typename= 'float'">%f</xsl:when>
		  <xsl:when test="@typename= 'bool'">%d</xsl:when>
		  <xsl:when test="@typename= 'util.Date'">%s</xsl:when>
		  <xsl:otherwise>= %c</xsl:otherwise>
    	</xsl:choose>
		
	    <xsl:if test="position() != last()"> and </xsl:if>
	  </xsl:for-each>
	  <xsl:text>
          ',&#10;</xsl:text>
	  <xsl:for-each select="attribute[@identity= 'false']">
	    <xsl:text>          </xsl:text>
	    <xsl:value-of select="concat('$this->', @name)"/>
		<xsl:if test="position() != last()">,&#10;</xsl:if>
	  </xsl:for-each>
	  <xsl:if test="count(attribute[@identity= 'true']) &gt; 0">
        <xsl:text>,&#10;</xsl:text>
	  </xsl:if>
	  <xsl:for-each select="attribute[@identity= 'true']">
	    <xsl:text>          </xsl:text>
	    <xsl:value-of select="concat('$this->', @name)"/>
		<xsl:if test="position() != last()">,&#10;</xsl:if>
	  </xsl:for-each>
      <xsl:text>
        );
      } if (catch('SQLException', $e)) {

        // more error handling TBD here?
        return throw($e);
      }

      return TRUE;
    }
    </xsl:text>
	
	<!-- Create insert() method -->
	<xsl:text>
    /**
     * Write this object to the database
     *
     * @access  public
     * @return  boolean success
     * @throws  SQLException in case an error occurs
     * @throws  IllegalAccessException in case there is no suitable database connection available
     */
    function insert() {
      $cm= &amp;ConnectionManager::getInstance();  
      if (FALSE === ($db= &amp;$cm->getByHost('***', 0))) {
        return throw(new IllegalAccessException('No connection available'));
      }

      try(); {
        $db->insert('
          </xsl:text>
			 <xsl:value-of select="@name"/><xsl:text> (&#10;</xsl:text>
				<xsl:for-each select="attribute[@identity = 'false']">
				  <xsl:text>            </xsl:text>
				  <xsl:value-of select="@name"/>
				  <xsl:if test="position() != last()">,&#10;</xsl:if>
				</xsl:for-each>
				<xsl:text>&#10;          ) values (&#10;            </xsl:text>
    			<xsl:for-each select="attribute[@identity = 'false']">
				  <xsl:choose>
	    			<xsl:when test="@typename= 'int'">%d</xsl:when>
					<xsl:when test="@typename= 'string'">%s</xsl:when>
					<xsl:when test="@typename= 'float'">%f</xsl:when>
					<xsl:when test="@typename= 'bool'">%d</xsl:when>
					<xsl:when test="@typename= 'util.Date'">%s</xsl:when>
					<xsl:otherwise>= %c</xsl:otherwise>
    			  </xsl:choose>

	    		  <xsl:if test="position() != last()">, </xsl:if>
				</xsl:for-each>
				<xsl:text>&#10;         )',&#10;</xsl:text>
				<xsl:for-each select="attribute[@identity = 'false']">
				  <xsl:text>          </xsl:text>
				  <xsl:value-of select="concat('$this->', @name)"/>
				  <xsl:if test="position() != last()">,&#10;</xsl:if>
				</xsl:for-each>
				<xsl:text>
        );&#10;</xsl:text>
				<xsl:if test="count(attribute[@identity = 'true']) = 1">
	    		  <xsl:text>
        // Fetch identity
        </xsl:text>
	    		  <xsl:value-of select="concat('$this->', attribute[@identity = 'true']/@name, '= ')"/>
				  <xsl:text>$db->insert_id();</xsl:text>
				</xsl:if>
				<xsl:text>
      } if (catch('SQLException', $e)) {

        // more error handling TBD here?
        return throw($e);
      }

      return TRUE;
    }
    </xsl:text>
	
    <!-- Closing curly brace -->	
    <xsl:text>&#10;  }</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
