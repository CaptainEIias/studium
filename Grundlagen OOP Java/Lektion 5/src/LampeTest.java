public class LampeTest{
	public static void main(String[] args) {
		Lampe l = new Lampe("L1", "Wohnzimmerlampe");
		l.einschalten();
		l.dimmen(60);
		System.out.println(l.toString());
		
		Geraet g = l;
		System.out.println(g.toString());
		g.ausschalten();
		
		Object o = g;
		System.out.println(o.toString());
		
	}
}