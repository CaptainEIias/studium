
public class RaumVollException extends Exception{
	public RaumVollException(String message) {
		super(message);
	}
	public RaumVollException() {
		super("Es können keine Geräte mehr hinzugefügt werden");
	}

}
