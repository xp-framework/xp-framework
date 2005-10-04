/* This class is part of the XP framework
 *
 * $Id$ 
 */

package net.xp_framework.unittest;

import org.junit.Test;
import org.junit.Before;
import static org.junit.Assert.*;

import net.xp_framework.fop.TemplateLoader;

public class TemplateLoaderTest {
  protected TemplateLoader loader;
  
  @Before public void setUp() {
    this.loader= new TemplateLoader();
  }
  
  @Test public void testAbstract() {
    assertEquals(this.loader.nameFor("de.schlund.cancel.fop"), "de/schlund/cancel/fop");
  }
}
