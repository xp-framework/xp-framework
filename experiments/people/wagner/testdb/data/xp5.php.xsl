<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:exslt="http://exslt.org/common"
  xmlns:func="http://exslt.org/functions"
  xmlns:string="http://exslt.org/strings"
  xmlns:my="http://no-sense.de/my"
  extension-element-prefixes="func exslt string"
>
  <xsl:output method="text" omit-xml-declaration="yes"/>
  
  <xsl:param name="path" />
  <xsl:param name="package" />
  <xsl:param name="prefix" />
  <xsl:param name="incprefix" />
  <xsl:param name="exprefix" />
  <xsl:param name="prefixRemove" />

  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>
  <xsl:variable name="this" select="/document" />
  
  <func:function name="my:ucfirst">
    <xsl:param name="string" />
    <func:result select="concat(
      translate(substring($string, 1, 1), $lcletters, $ucletters),
      translate(substring($string, 2), $ucletters, $lcletters)
    )"/>
  </func:function>

  <func:function name="my:constraintSingleTest">
    <xsl:param name="keyset" />
    <xsl:param name="sourceIndexSet" />
    <xsl:choose>
      <xsl:when test="count(exslt:node-set($keyset)) = 0"><func:result select="false()" /></xsl:when>
      <xsl:when test="count(exslt:node-set($sourceIndexSet)) = 0"><func:result select="false()" /></xsl:when>
      <xsl:when test="my:keysetSingleTest($keyset, exslt:node-set($sourceIndexSet)[1]/key)"><func:result select="true()" /></xsl:when>
      <xsl:otherwise><func:result select="my:constraintSingleTest($keyset, exslt:node-set($sourceIndexSet)[position() != 1])" /></xsl:otherwise>
    </xsl:choose>
  </func:function>

  <func:function name="my:keysetSingleTest">
    <xsl:param name="keyset" />
    <xsl:param name="sourceKeyset" />
    <xsl:choose>
      <xsl:when test="boolean(count(exslt:node-set($keyset)) = 0) and boolean(count(exslt:node-set($sourceKeyset)) = 0)"><func:result select="true()" /></xsl:when>
      <xsl:when test="count(exslt:node-set($keyset)) = 0"><func:result select="false()" /></xsl:when>
      <xsl:when test="count(exslt:node-set($sourceKeyset)) = 0"><func:result select="false()" /></xsl:when>
      <xsl:otherwise>
        <xsl:variable name="testkey" select="exslt:node-set($keyset)[1]/@sourceattribute" />
        <xsl:choose>
          <xsl:when test="count(exslt:node-set($keyset)[@sourceattribute = $testkey]) != count(exslt:node-set($sourceKeyset)[text() = $testkey])"><func:result select="false()" /></xsl:when>
          <xsl:otherwise>
            <func:result select="my:keysetSingleTest(exslt:node-set($keyset)[@sourceattribute != $testkey], exslt:node-set($sourceKeyset)[text() != $testkey])" />
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </func:function>

  <func:function name="my:prefixedClassName">
    <xsl:param name="tname" />
    <xsl:param name="prefix"  select="$prefix" />
    <xsl:param name="include" select="$incprefix" />
    <xsl:param name="exclude" select="$exprefix" />
    <xsl:param name="remove"  select="$prefixRemove" />
    <xsl:variable name="includeSet" select="string:tokenize($include, ',')" />
    <xsl:variable name="excludeSet" select="string:tokenize($exclude, ',')" />
    <xsl:variable name="excludetest" select="boolean(count($includeSet) = 0) and not(count($excludeSet) = 0) and not($excludeSet/*[name() = $tname])" />
    <xsl:variable name="includetest" select="not(count($includeSet) = 0) and boolean(count($excludeSet) = 0) and boolean($includeSet/*[name() = $tname])" />
    <xsl:variable name="p">
      <xsl:choose>
        <xsl:when test="$includetest or $excludetest"><xsl:value-of select="$prefix" /></xsl:when>
        <xsl:otherwise><xsl:value-of select="''" /></xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <xsl:variable name="r">
      <xsl:choose>
        <xsl:when test="$includetest or $excludetest"><xsl:value-of select="$remove" /></xsl:when>
        <xsl:otherwise><xsl:value-of select="''" /></xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$r = substring($tname, 1, string-length($r))">
        <func:result select="concat($p, my:ucfirst(substring($tname, string-length($r) + 1)))" />
      </xsl:when>
      <xsl:otherwise>
        <func:result select="concat($p, my:ucfirst($tname))" />
      </xsl:otherwise>
    </xsl:choose>
  </func:function>
  
  <func:function name="my:separator">
    <xsl:param name="database"/>
    <xsl:param name="table"/>
    <xsl:param name="dbtype"/>
    <xsl:choose>
      <xsl:when test="$dbtype = 'pgsql'"><func:result select="$table"/></xsl:when>
      <xsl:when test="$dbtype = 'mysql'"><func:result select="concat($database, '.', $table)"/></xsl:when>
      <xsl:when test="$dbtype = 'sybase'"><func:result select="concat($database, '..', $table)"/></xsl:when>
    </xsl:choose>
  </func:function>
  
  <xsl:template match="/">

    <xsl:text>&lt;?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');&#10;</xsl:text>
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
  class </xsl:text><xsl:value-of select="@class"/><xsl:text> extends DataSet {
    public&#10;</xsl:text>

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
  <xsl:text>;
  
    private
      $_cached=   array(),</xsl:text>
  <xsl:for-each select="constraint/reference | document(concat('../', $path, '/../constraints/constraints.xml'))/document/database[@database = $this/table/@database]/table/constraint/reference[@table = $this/table/@name]"><xsl:if test="position() != 1"><xsl:text>,</xsl:text></xsl:if><xsl:text>
      $cache</xsl:text><xsl:value-of select="@role" /><xsl:text>= array()</xsl:text>
  </xsl:for-each><xsl:text>;
  
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('</xsl:text><xsl:value-of select="my:separator(@database, @name, @dbtype)"/><xsl:text>');
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
        $peer->setConstraints(array(</xsl:text>
  <xsl:for-each select="constraint/reference"><xsl:text>
          '</xsl:text><xsl:value-of select="@role" /><xsl:text>' => array(
            'classname' => '</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))" /><xsl:text>',
            'key'       => array(
              </xsl:text><xsl:for-each select="key"><xsl:text>'</xsl:text><xsl:value-of select="@attribute" /><xsl:text>' => '</xsl:text><xsl:value-of select="@sourceattribute" /><xsl:text>',</xsl:text></xsl:for-each><xsl:text>
            ),
          ),</xsl:text>
  </xsl:for-each>
  <xsl:for-each select="document(concat('../', $path, '/../constraints/constraints.xml'))/document/database[@database = $this/table/@database]/table/constraint/reference[@table = $this/table/@name]"><xsl:text>
          '</xsl:text><xsl:value-of select="@role" /><xsl:text>' => array(
            'classname' => '</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))" /><xsl:text>',
            'key'       => array(
              </xsl:text><xsl:for-each select="key">'<xsl:value-of select="@sourceattribute" /><xsl:text>' => '</xsl:text><xsl:value-of select="@attribute" /><xsl:text>',</xsl:text></xsl:for-each><xsl:text>
            ),
          ),</xsl:text>
  </xsl:for-each><xsl:text>
        ));
      }
    }  

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }</xsl:text>
  <xsl:for-each select="constraint/reference | document(concat('../', $path, '/../constraints/constraints.xml'))/document/database[@database = $this/table/@database]/table/constraint/reference[@table = $this/table/@name]"><xsl:text>
    public function _cacheGet</xsl:text><xsl:value-of select="@role" /><xsl:text>($key) { return $this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>[$key]; }
    public function _cacheHas</xsl:text><xsl:value-of select="@role" /><xsl:text>($key) { return isset($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>[$key]); }
    public function _cacheAdd</xsl:text><xsl:value-of select="@role" /><xsl:text>($key, $obj) { $this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>[$key]= $obj; }</xsl:text>
  </xsl:for-each><xsl:text>

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
    }
    
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
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
      <xsl:if test="not(@unique= 'true')">[] entity objects</xsl:if>
      <xsl:if test="@unique= 'true'"> entitiy object</xsl:if>
    <xsl:text>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getBy</xsl:text>
    <xsl:for-each select="key"><xsl:value-of select="my:ucfirst(text())" /></xsl:for-each>
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
    <xsl:text>    }&#10;</xsl:text>
  </xsl:for-each>

  <!-- Create getters and setters -->
    <xsl:for-each select="attribute">
      <xsl:text>
    /**
     * Retrieves </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="@typename"/><xsl:text>
     */
    public function get</xsl:text><xsl:value-of select="my:ucfirst(@name)" /><xsl:text>() {
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
    public function set</xsl:text><xsl:value-of select="my:ucfirst(@name)" /><xsl:text>(</xsl:text>$<xsl:value-of select="@name"/><xsl:text>) {
      return $this->_change('</xsl:text><xsl:value-of select="@name"/><xsl:text>', $</xsl:text><xsl:value-of select="@name"/><xsl:text>);
    }&#10;</xsl:text>
  </xsl:for-each>

  <!-- create referenced object getters -->
  <xsl:for-each select="constraint/reference">
    <xsl:variable name="referencedTable" select="document(concat('../', $path, '/', my:ucfirst(@table), '.xml'))/document" />
    <xsl:variable name="isSingle" select="my:constraintSingleTest(./key, $referencedTable/table/index[@unique = 'true'])" />
    <xsl:choose>
      <!-- case referenced fields are unique -->
      <xsl:when test="$isSingle">
        <xsl:text>
    /**
     * Retrieves the </xsl:text><xsl:value-of select="my:ucfirst(@table)"/><xsl:text> entity
     * referenced by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@sourceattribute" />=><xsl:value-of select="@attribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))"/><xsl:text> entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>() {
      $r= ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) ?
        array_values($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>) :
        XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))" /><xsl:text>')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(&#10;</xsl:text>
          <xsl:for-each select="key">
            <xsl:text>          array('</xsl:text><xsl:value-of select="@sourceattribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@attribute)" /><xsl:text>(), EQUAL)</xsl:text>
            <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
            <xsl:text>&#10;</xsl:text>
          </xsl:for-each>
        <xsl:text>      ));
      return $r ? $r[0] : NULL;&#10;    }&#10;</xsl:text>
      </xsl:when>
      <!-- case referenced fields are not unique -->
      <xsl:otherwise>
        <xsl:text>
    /**
     * Retrieves an array of all </xsl:text><xsl:value-of select="my:ucfirst(@table)"/><xsl:text> entities
     * referenced by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@sourceattribute" />=><xsl:value-of select="@attribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))"/><xsl:text>[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>List() {
      if ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) return array_values($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>);
      return XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))" /><xsl:text>')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(&#10;</xsl:text>
        <xsl:for-each select="key">
          <xsl:text>        array('</xsl:text><xsl:value-of select="@sourceattribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@attribute)" /><xsl:text>(), EQUAL)</xsl:text>
          <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
          <xsl:text>&#10;</xsl:text>
        </xsl:for-each>
      <xsl:text>      ));
    }

    /**
     * Retrieves an iterator for all </xsl:text><xsl:value-of select="my:ucfirst(@table)"/><xsl:text> entities
     * referenced by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@sourceattribute" />=><xsl:value-of select="@attribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  rdbms.ResultIterator&lt;</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))"/><xsl:text>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>Iterator() {
      if ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) return new HashmapIterator($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>);
      return XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(@table))" /><xsl:text>')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(&#10;</xsl:text>
        <xsl:for-each select="key">
          <xsl:text>        array('</xsl:text><xsl:value-of select="@sourceattribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@attribute)" /><xsl:text>(), EQUAL)</xsl:text>
          <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
          <xsl:text>&#10;</xsl:text>
        </xsl:for-each>
      <xsl:text>      ));&#10;    }&#10;</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:for-each>
  

  <!-- create referencing object getters -->
  <xsl:for-each select="document(concat('../', $path, '/../constraints/constraints.xml'))/document/database[@database = $this/table/@database]/table/constraint/reference[@table = $this/table/@name]">
    <xsl:variable name="referencingTable" select="document(concat('../', $path, '/', my:ucfirst(../../@name), '.xml'))/document" />
    <xsl:variable name="isSingle" select="my:constraintSingleTest(./key, $referencingTable/table/index[@unique = 'true'])" />
    <xsl:choose>
      <xsl:when test="$isSingle">

        <xsl:text>
    /**
     * Retrieves the </xsl:text><xsl:value-of select="my:ucfirst(../../@name)"/><xsl:text> entity referencing
     * this entity by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@attribute" />=><xsl:value-of select="@sourceattribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))"/><xsl:text> entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>() {
      $r= ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) ?
        array_values($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>) :
        XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))" /><xsl:text>')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(&#10;</xsl:text>
          <xsl:for-each select="key">
            <xsl:text>          array('</xsl:text><xsl:value-of select="@attribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@sourceattribute)" /><xsl:text>(), EQUAL)</xsl:text>
            <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
            <xsl:text>&#10;</xsl:text>
          </xsl:for-each>
        <xsl:text>      ));
      return $r ? $r[0] : NULL;&#10;    }&#10;</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>
    /**
     * Retrieves an array of all </xsl:text><xsl:value-of select="my:ucfirst(../../@name)"/><xsl:text> entities referencing
     * this entity by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@attribute" />=><xsl:value-of select="@sourceattribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  </xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))"/><xsl:text>[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>List() {
      if ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) return array_values($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>);
      return XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))" /><xsl:text>')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(&#10;</xsl:text>
        <xsl:for-each select="key">
          <xsl:text>          array('</xsl:text><xsl:value-of select="@attribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@sourceattribute)" /><xsl:text>(), EQUAL)</xsl:text>
          <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
          <xsl:text>&#10;</xsl:text>
        </xsl:for-each>
      <xsl:text>      ));
    }

    /**
     * Retrieves an iterator for all </xsl:text><xsl:value-of select="my:ucfirst(../../@name)"/><xsl:text> entities referencing
     * this entity by </xsl:text><xsl:for-each select="key"><xsl:value-of select="@attribute" />=><xsl:value-of select="@sourceattribute" /><xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>
     *
     * @return  rdbms.ResultIterator&lt;</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))"/><xsl:text>>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function get</xsl:text><xsl:value-of select="@role" /><xsl:text>Iterator() {
      if ($this->_cached['</xsl:text><xsl:value-of select="@role" /><xsl:text>']) return new HashmapIterator($this->cache</xsl:text><xsl:value-of select="@role" /><xsl:text>);
      return XPClass::forName('</xsl:text><xsl:value-of select="concat($package, '.', my:prefixedClassName(../../@name))" /><xsl:text>')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(&#10;</xsl:text>
        <xsl:for-each select="key">
          <xsl:text>          array('</xsl:text><xsl:value-of select="@attribute" /><xsl:text>', $this->get</xsl:text><xsl:value-of select="my:ucfirst(@sourceattribute)" /><xsl:text>(), EQUAL)</xsl:text>
          <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
          <xsl:text>&#10;</xsl:text>
        </xsl:for-each>
      <xsl:text>      ));&#10;    }&#10;</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:for-each>
  
    <!-- Closing curly brace -->  
    <xsl:text>  }</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
