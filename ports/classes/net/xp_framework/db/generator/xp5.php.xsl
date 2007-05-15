<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" omit-xml-declaration="yes"/>
  
  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>
  
  <xsl:template name="separator">
    <xsl:param name="database"/>
    <xsl:param name="table"/>
    <xsl:param name="dbtype"/>
    
    <xsl:choose>
      <xsl:when test="$dbtype = 'pgsql'"><xsl:value-of select="$table"/></xsl:when>
      <xsl:when test="$dbtype = 'mysql'"><xsl:value-of select="concat($database, '.', $table)"/></xsl:when>
      <xsl:when test="$dbtype = 'sybase'"><xsl:value-of select="concat($database, '..', $table)"/></xsl:when>
    </xsl:choose>
  </xsl:template>
  
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
 
  uses('rdbms.DataSet');
 </xsl:text>
      <xsl:apply-templates/>
  <xsl:text>?></xsl:text>
  </xsl:template>
  
  <xsl:template match="table">
    <xsl:variable name="primary_key_unique" select="index[@primary= 'true' and @unique= 'true']/key/text()"/>

    <xsl:text>/**
   * Class wrapper for table </xsl:text><xsl:value-of select="@name"/>, database <xsl:value-of select="./@database"/><xsl:text>
   * (Auto-generated on </xsl:text><xsl:value-of select="concat(
     ../@created_at, 
   ' by ', 
   ../@created_by
   )"/><xsl:text>)
   *
   * @purpose  Datasource accessor
   */
  class </xsl:text><xsl:value-of select="@class"/><xsl:text> extends DataSet {&#10;    public&#10;</xsl:text>
  
  <!-- Attributes -->
  <xsl:for-each select="attribute">
    <xsl:value-of select="concat('      $', @name, substring('                                ', 0, 20 - string-length(@name)))"/>
    <xsl:choose>
      <xsl:when test="@nullable = 'true'">= NULL</xsl:when>
      <xsl:when test="@typename= 'int'">= 0</xsl:when>
      <xsl:when test="@typename= 'string'">= ''</xsl:when>
      <xsl:when test="@typename= 'float'">= 0.0</xsl:when>
      <xsl:when test="@typename= 'bool'">= FALSE</xsl:when>
      <xsl:when test="@typename= 'util.Date'">= NULL</xsl:when>
    </xsl:choose>
    <xsl:if test="position() != last()">,&#10;</xsl:if>
  </xsl:for-each>
  <xsl:text>;&#10;</xsl:text>
  
  <!-- Create static initializer -->
  <xsl:text>
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('</xsl:text><xsl:call-template name="separator">
          <xsl:with-param name="database" select="@database"/>
          <xsl:with-param name="table" select="@name"/>
          <xsl:with-param name="dbtype" select="@dbtype"/>
        </xsl:call-template><xsl:text>');
        $peer->setConnection('</xsl:text><xsl:value-of select="@dbhost"/><xsl:text>');</xsl:text>
        <xsl:if test="attribute[@identity= 'true']">
          <xsl:text>&#10;        $peer->setIdentity('</xsl:text><xsl:value-of select="attribute[@identity= 'true']/@name"/><xsl:text>');</xsl:text>
        </xsl:if><xsl:text>
        $peer->setPrimary(array('</xsl:text>
          <xsl:for-each select="index[@primary= 'true']/key">
            <xsl:value-of select="."/>
            <xsl:if test="position() != last()">', '</xsl:if>
          </xsl:for-each>
        <xsl:text>'));
        $peer->setTypes(array(&#10;</xsl:text>
  <xsl:for-each select="attribute">
    <xsl:text>          '</xsl:text>
    <xsl:value-of select="@name"/>'<xsl:value-of select="substring('                                ', 0, 20 - string-length(@name))"/>
    <xsl:text> =&gt; array('</xsl:text>
    <xsl:choose>
      <xsl:when test="@typename= 'int'">%d</xsl:when>
      <xsl:when test="@typename= 'string'">%s</xsl:when>
      <xsl:when test="@typename= 'float'">%f</xsl:when>
      <xsl:when test="@typename= 'bool'">%d</xsl:when>
      <xsl:when test="@typename= 'util.Date'">%s</xsl:when>
      <xsl:otherwise>%c</xsl:otherwise>
    </xsl:choose>
    <xsl:text>', FieldType::</xsl:text>
    <xsl:value-of select="substring-after(@type, 'DB_ATTRTYPE_')"/>
    <xsl:text>, </xsl:text>
    <xsl:value-of select="translate(@nullable, $lcletters, $ucletters)"/>
    <xsl:text>)</xsl:text>
    <xsl:if test="position() != last()">,&#10;</xsl:if>
  </xsl:for-each>
  <xsl:text>
        ));
      }
    }  
  </xsl:text>

  <!-- Create getPeer() method -->
  <xsl:text>
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  </xsl:text>

  <!-- Create a static method for indexes -->
  <xsl:for-each select="index[@name != '' and string-length (key/text()) != 0]">
    <xsl:text>
    /**
     * Gets an instance of this object by index "</xsl:text><xsl:value-of select="@name"/><xsl:text>"
     * </xsl:text>
    <xsl:for-each select="key">
      <xsl:variable name="key" select="text()"/>
    <xsl:text>
     * @param   </xsl:text>
      <xsl:value-of select="concat(../../attribute[@name= $key]/@typename, ' ', $key)"/>
    </xsl:for-each>
    <xsl:text>
     * @return  </xsl:text><xsl:value-of select="concat(../@package, '.', ../@class)"/>
    <xsl:choose>
      <xsl:when test="not(@unique= 'true')">[] entities</xsl:when>
      <xsl:otherwise> entity</xsl:otherwise>
    </xsl:choose>
    <xsl:text>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getBy</xsl:text>
    <xsl:for-each select="key">
      <xsl:call-template name="prettyname">
        <xsl:with-param name="string" select="text()"/>
      </xsl:call-template>
    </xsl:for-each>
    <xsl:text>(</xsl:text>
    <xsl:for-each select="key">
      <xsl:value-of select="concat('$', text())"/>
    <xsl:if test="position() != last()">, </xsl:if>
    </xsl:for-each>
    <xsl:text>) {&#10;</xsl:text>
      <xsl:choose>
        <xsl:when test="count(key) = 1">
        
          <!-- Single key -->
          <xsl:choose>
            <xsl:when test="@unique = 'true'">
              <xsl:text>      $r= self::getPeer()-&gt;doSelect(new Criteria(array('</xsl:text>
              <xsl:value-of select="key"/>
              <xsl:text>', $</xsl:text>
              <xsl:value-of select="key"/>
              <xsl:text>, EQUAL)));&#10;      return $r ? $r[0] : NULL;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>      return self::getPeer()-&gt;doSelect(new Criteria(array('</xsl:text>
              <xsl:value-of select="key"/>
              <xsl:text>', $</xsl:text>
              <xsl:value-of select="key"/>
              <xsl:text>, EQUAL)));</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
        
          <!-- Multiple keys -->
          <xsl:choose>
            <xsl:when test="@unique = 'true'">
              <xsl:text>      $r= self::getPeer()-&gt;doSelect(new Criteria(&#10;</xsl:text>
              <xsl:for-each select="key">
                <xsl:text>        array('</xsl:text>
                <xsl:value-of select="."/>
                <xsl:text>', $</xsl:text>
                <xsl:value-of select="."/>
                <xsl:text>, EQUAL)</xsl:text>
                <xsl:if test="position() != last()">,</xsl:if><xsl:text>&#10;</xsl:text>
              </xsl:for-each>
              <xsl:text>      ));&#10;      return $r ? $r[0] : NULL;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>      return self::getPeer()-&gt;doSelect(new Criteria(&#10;</xsl:text>
              <xsl:for-each select="key">
                <xsl:text>        array('</xsl:text>
                <xsl:value-of select="."/>
                <xsl:text>', $</xsl:text>
                <xsl:value-of select="."/>
                <xsl:text>, EQUAL)</xsl:text>
                <xsl:if test="position() != last()">,</xsl:if><xsl:text>&#10;</xsl:text>
              </xsl:for-each>
              <xsl:text>      ));</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:otherwise>
      </xsl:choose>
    <xsl:text>&#10;    }&#10;</xsl:text>
  </xsl:for-each>
  
  <!-- Create getters and setters -->
    <xsl:for-each select="attribute">
      <xsl:text>
    /**
     * Retrieves </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="@typename"/><xsl:text>
     */
    public function </xsl:text>
    <xsl:text>get</xsl:text>
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
     * @param   </xsl:text><xsl:value-of select="concat(@typename, ' ', @name)"/><xsl:text>
     * @return  </xsl:text><xsl:value-of select="@typename"/><xsl:text> the previous value
     */
    public function </xsl:text><xsl:text>set</xsl:text>
    <xsl:call-template name="prettyname">
    <xsl:with-param name="string" select="@name"/>
    </xsl:call-template>
      <xsl:text>(</xsl:text>$<xsl:value-of select="@name"/><xsl:text>) {
      return $this->_change('</xsl:text><xsl:value-of select="@name"/><xsl:text>', $</xsl:text><xsl:value-of select="@name"/><xsl:text>);
    }&#10;</xsl:text>
  </xsl:for-each>
  
    <!-- Closing curly brace -->  
    <xsl:text>  }</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
