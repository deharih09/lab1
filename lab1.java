import java.util.Random;
import java.util.Scanner;


public class lab1 {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		Scanner in = new Scanner(System.in);
		Random generator = new Random();
		int number = generator.nextInt(100) + 1;
		System.out.println("Please guess a number between 1 and 
100");
		int guess = in.nextInt();
		if (number == guess){
			System.out.println("You guessed the correct 
number: " + guess);
		}
		else{
			System.out.print("Sorry, your guess did not 
match the number");
		}
                             //testtest

	}

}
