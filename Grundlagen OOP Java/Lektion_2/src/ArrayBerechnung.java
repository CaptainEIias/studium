public class ArrayBerechnung{
	public static void main(String[] args) {
		int[] arr1 = {5,8,2,15,7,1,22,3,14,9};
		int min1 = arr1[0];
		for (int i = 0; i < arr1.length; i++) {
			if (arr1[i] < min1) {
				min1 = arr1[i];
				System.out.println(": "+min1+" "+i);
			}
		}
		System.out.println("Array 1:");
		System.out.print(" Minimalwert: "+min1);
		System.out.println();
		
		int[] arr2 = {11,4,19,0,17,8,13,6,5,12,24,3,21};
		int min2 = arr2[0];
		for (int i = 0; i<arr2.length; i++) {
			if(arr2[i] < min2) {
				min2 = arr2[i];
			}
		}
		System.out.println("Array 2:");
		System.out.print(" Minimalwert: "+min2);
		
	}
}