
public class TryAnweisungTest {
	public static void main(String[] args) {
		int zaehler = 10;
		int nenner = 0;
		
		try {
			int ergebnis = zaehler / nenner;
			System.out.println("Ergebnis: "+ergebnis);
		} catch(ArithmeticException e) {
			System.out.println("Division durch null ist nicht erlaubt.");
			System.out.println("Fehlermeldung von Java: "+e.getMessage());
		}
		
	}
}
