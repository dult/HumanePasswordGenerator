<?php

/* Humane Password Generator
 * A password generator designed to create easy to remember and secure passwords.
 * Based on XKCD comic 936 "Password Strength" with some changes inserted.
 * By DuLt Nuno Pinto
 */

class HPG {
	function random( $chars, $amount ) {
		$rand = substr(str_shuffle(str_repeat($chars, $amount)), 0, $amount);
		return $rand;
	}
	function upperLower( $word ) {
		$rand = $this->random( 'LUIP', 1 );

		if ( $rand === 'L' ) {
			$word = strtolower( $word );
		} else if ( $rand === 'U' ) {
			$word = strtoupper( $word );
		} else if ( $rand === 'I' ) {
			$word = strtoupper( lcfirst( $word ) );
		} else if ( $rand === 'P' ) {
			$word = ucwords( $word );
		}
		return $word;
	}
	function generate( $db_name, $length, $complexity ) {
		$maxLength = 5;
		$maxComplexity = 2;
		$password = '';

		if ( $length < 1 ) {
			$length = 1;
		} else if ( $length > $maxLength ) {
			$length = $maxLength;
		}
		if ( $complexity < 1 ) {
			$complexity = 1;
		} else if ( $complexity > $maxComplexity ) {
			$complexity = $maxComplexity;
		}

		$db = new PDO('sqlite:' . $db_name );
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = 'SELECT * FROM words ORDER BY RANDOM() LIMIT :length';
		$stmt = $db->prepare($sql);
		$stmt->bindValue( ':length', $length, SQLITE3_INTEGER );
		$stmt->execute();
		$words = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$space = $this->random( '*_-+.', 1 );

		for ( $p = 0; $p < $length; $p++ ) {

			$word = $words[$p]['word'];

			if ( $complexity <= 1 ) {
				if ( $p === 0 ) {
					$word = ucwords( $word );
				}
			} else {
				$word = $this->upperLower( $word );
				$space = $this->random( '*_-+.', 1 );
			}



			$password = $password . $word . $space;
		}
		$numbers = $this->random( '01234567890123456789', 2 );
		$end = '';
		
		if ( $complexity > 1 ) {
			$end = $this->random( '!@#$%&*_-+=:;,.?', 1 );
		}

		$password = $password . $numbers . $end;

		$test1 = preg_match('@[A-Z]@', $password);
		$test2 = preg_match('@[a-z]@', $password);
		$test3 = preg_match('@[0-9]@', $password);
		$test4 = preg_match('@[^\w]@', $password);

		return($password);
	}
}
// Sample usage
/*
$password = new HPG();
echo $password->generate('words.sqlite', 2, 1);
echo '<br>';
echo $password->generate('words.sqlite', 3, 1);
echo '<br>';
echo $password->generate('words.sqlite', 2, 2);
echo '<br>';
echo $password->generate('words.sqlite', 3, 2);
*/
