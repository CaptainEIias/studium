public abstract class Geraet implements Schaltbar{
	private String geraetId;
	private String name;
	private boolean an;
	
	public Geraet(String geraetId, String name) {
		this.geraetId = geraetId;
		this.name = name;
		this.an = false;
	}
	
	public void setName(String name) {
		this.name = name;
	}
	
	public String getName() {
		return this.name;
	}
	
	@Override
	public void einschalten() {
		an = true;
	}
	
	@Override
	public void ausschalten() {
		an = false;
	}
	
	@Override
	public boolean isAn() {
		return an;
	}
	
	@Override 
	public String toString() {
		return "Gerätenummer: "+ geraetId + ", Name; "+name+", an: "
				+ ""+an;
	}
	
}