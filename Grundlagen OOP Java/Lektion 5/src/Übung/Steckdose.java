package Übung;

public class Steckdose extends Geraet{
	private double verbrauch;
	
	public Steckdose(String geraetId, String name) {
		super(geraetId, name);
	}
	
	public double getVerbrauch() {
		return verbrauch;
	}
}
