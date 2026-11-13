public class StringTest{
	public static void main(String[] args) {
		String name = "Pizza Margherita";
		String info = new String("Der Lieferservice ist schnell.");
		System.out.println("Länge von name: "+ name.length());
		
		char ersterBuchstabe = name.charAt(0);
		System.out.println("Erster Buchstabe im Namen: "+ ersterBuchstabe);
		
		boolean enthaeltWort = info.contains("schnell");
		System.out.println("Info enthält 'schnell'? "+ enthaeltWort);
	}
}