import net.php.serialize.Serializer;
import java.util.Date;

class Person {
  public int id;
  public String firstname;
  public String lastname;
  public Date birthDay; 
} 

public class Test {
  public static void main(String[] args) {
    Person p = new Person(); {
      p.id = 1549;
      p.firstname= "Timm";
      p.lastname= "Friebe";
      p.birthDay= new Date(77, 11, 14, 11, 55);
    }
    System.out.println(Serializer.serialize(p));
  }
}
