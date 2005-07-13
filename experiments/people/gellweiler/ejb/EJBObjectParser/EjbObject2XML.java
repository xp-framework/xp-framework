import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import java.util.Arrays;
import java.util.Comparator;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Result;
import javax.xml.transform.Source;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.xml.sax.SAXException;

/**
 * @author gelli
 *  
 */
public class EjbObject2XML {

	public static void main(String[] args) {

		// Classname given ?
		if (2 == args.length) {
			System.out.println("No classname given");
			return;
		}

		// Initialize variables
		Class cls = null;
		DocumentBuilder domBuilder = null;
		String classname = args[0].substring(1 + args[0].lastIndexOf("."));
		Document doc = null;
		Document docXSLT = null;
		DOMSource dsXSLT = null;
		String xslFilename = "xp.php.xsl";

		try {
			DocumentBuilderFactory domFactory = DocumentBuilderFactory
					.newInstance();
			domFactory.setNamespaceAware(true);
			domBuilder = domFactory.newDocumentBuilder();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return;
		}

		try {
			cls = Class.forName(args[0]);
			doc = domBuilder.newDocument();
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
			return;
		}

		// Start output
		Element rootelement = doc.createElement("interface");
		rootelement.setAttribute("name", classname);
		doc.appendChild(rootelement);

		Method[] methods = cls.getMethods();
		Arrays.sort(methods, new Comparator() {
			public int compare(Object o1, Object o2) {
				return ((Method) o1).getName().compareTo(
						((Method) o2).getName());
			}
		});

		for (int i = 0; i < methods.length; i++) {

			Element method = doc.createElement("method");
			method.setAttribute("name", methods[i].getName());
			Element returnparam = doc.createElement("return");
			returnparam.setAttribute("type", methods[i].getReturnType()
					.toString());
			method.appendChild(returnparam);
			rootelement.appendChild(method);

			// Add parameters
			Element parameters = doc.createElement("parameters");
			method.appendChild(parameters);
			Class[] params = methods[i].getParameterTypes();
			for (int j = 0; j < params.length; j++) {
				Element parameter = doc.createElement("parameter");
				parameter.setAttribute("type", params[j].getName());
				parameters.appendChild(parameter);
			}

			// Add thrown exceptions
			Element thrown = doc.createElement("throws");
			method.appendChild(thrown);
			Class[] exceptions = methods[i].getExceptionTypes();
			for (int j = 0; j < exceptions.length; j++) {
				Element exception = doc.createElement("exception");
				exception.setAttribute("type", exceptions[j].getName());
				thrown.appendChild(exception);
			}
		}

    // Save the document to disk
		try {
			docXSLT = domBuilder.parse(new File(xslFilename));
			dsXSLT = new DOMSource(docXSLT);
		} catch (SAXException e3) {
			// TODO Auto-generated catch block
			e3.printStackTrace();
		} catch (IOException e3) {
			// TODO Auto-generated catch block
			e3.printStackTrace();
		}

		TransformerFactory tranFactory = TransformerFactory.newInstance();
		Transformer aTransformer = null;
		try {
			aTransformer = tranFactory.newTransformer(dsXSLT);
		} catch (TransformerConfigurationException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		Source src = new DOMSource(doc);
		Result dest = null;
		try {
			dest = new StreamResult(new FileOutputStream(new File(classname
					+ ".class.php")));
		} catch (FileNotFoundException e4) {
			// TODO Auto-generated catch block
			e4.printStackTrace();
		}
		try {
			aTransformer.transform(src, dest);
		} catch (TransformerException e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
}
