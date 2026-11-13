
public class LampeV2 {
	private boolean an;
	private int helligkeit;
	
	public LampeV2(boolean an, int helligkeit) {
		this.an = an;
		this.helligkeit = helligkeit;
	}
	
	public boolean isAn() {
		return an;
	}
	
	public void setAn(boolean an) {
		this.an = an;
	}
	
	public int getHelligkeit() {
		return helligkeit;
	}
	
	public void setHelligkeit(int helligkeit) {
		this.helligkeit = helligkeit;
	}

}
