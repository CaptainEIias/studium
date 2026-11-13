
public interface IGeometrischeFigur {
	public abstract double berechneUmfang();
	public abstract double berechneFlaecheninhalt();
	
	default String ausgabe() {
		return "Umfang: "+berechneUmfang()+", "
				+ "Flächeninhalt: "+berechneFlaecheninhalt();
	}
	
}
