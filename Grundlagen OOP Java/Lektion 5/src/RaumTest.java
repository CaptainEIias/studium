public class RaumTest{
	public static void main(String[] args) {
		Raum wohnzimmer = new Raum("R1", "Wohnzimmer");
		Raum bad = new Raum("R2", "Badezimmer");
		
		Lampe stehlampe = new Lampe("L1", "Stehlampe");
		Thermostat tempWohnzimmer = new Thermostat("T1", "Thermostat Wohnen");
		Lampe esstischLampe = new Lampe("L2", "Esstischlampe");
		Lampe badSchrankLampe = new Lampe("L3", "Badschranklampe");
		Thermostat tempBad = new Thermostat("T2", "Thermostat Heizkörper");
		
		wohnzimmer.geraetHinzufuegen(stehlampe);
		wohnzimmer.geraetHinzufuegen(tempWohnzimmer);
		wohnzimmer.geraetHinzufuegen(esstischLampe);
		System.out.println("Geräte dem Wohnzimmer hinzugefügt!");
		
		bad.geraetHinzufuegen(badSchrankLampe);
		bad.geraetHinzufuegen(tempBad);
		System.out.println("Geräte dem Badzimmer hinzugefügt!");
		
		wohnzimmer.geraeteAusschalten();
		bad.geraeteAusschalten();
		System.out.println("Alle Geräte ausgeschaltet");
		
		System.out.println("\n###########\n");
		
		System.out.println(wohnzimmer.toString());
		System.out.println(wohnzimmer);
		
	}
}