using System;
using System.Collections.Generic;
using System.Text;
using Net.XpFramework.EASC;

using beans.test;

namespace TestRunnerClient
{
    class Program
    {
        static void Main(string[] args)
        {
            // Initialize
            try
            {
                Remote r = Remote.ForName("xp://localhost:6448");
                Console.WriteLine("Remote: {0}", r);

                // Lookup
                TestRunner runner = (TestRunner)r.Lookup("xp/test/TestRunner");
                Console.WriteLine("Lookup: {0}", runner);

                object result= runner.runTestClass("net.xp_framework.unittest.core.ObjectTest");
                Console.WriteLine("Result: {0}", result);
            } catch (RemoteException e) {
                Console.WriteLine("*** {0}", e);
            }
        }
    }
}
