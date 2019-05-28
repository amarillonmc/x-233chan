<?php
/*
 * This file is part of kusaba.
 *
 * kusaba is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * kusaba is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * kusaba; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

/***************************************************************************
*
* Author : Eric Sizemore ( www.secondversion.com & www.phpsociety.com )
* Package : Random Word
* Version : 1.0.1
* Copyright: (C) 2006 - 2007 Eric Sizemore
* Site	: www.secondversion.com & www.phpsociety.com
* Email	: esizemore05@gmail.com
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
***************************************************************************/

// Slightly inspired by class randomWord by kumar mcmillan
class Rand_Word {
	var $vowels = array('a','e','i','o','u');
	var $consonants = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','z','ch','qu','th','xy');
	var $word = '';

	/**
	* Constructor.
	*
	* @param integer Length of the word
	* @param boolean Return the word lowercase?
	* @param boolean Reutrn the word with the first letter capitalized?
	* @param boolean Return the word uppercase?
	* @return string
	*/
	function rand_word($length = 5, $lower_case = true, $ucfirst = false, $upper_case = false)
	{
		$done = false;
		$const_or_vowel = 1;

		while (!$done)
		{
			switch ($const_or_vowel)
			{
				case 1:
					$this->word .= $this->consonants[array_rand($this->consonants)];
					$const_or_vowel = 2;
					break;
				case 2:
					$this->word .= $this->vowels[array_rand($this->vowels)];
					$const_or_vowel = 1;
					break;
			}

			if (strlen($this->word) >= $length)
			{
				$done = true;
			}
		}

		$this->word = substr($this->word, 0, $length);

		if ($lower_case)
		{
			$this->word = strtolower($this->word);
		}
		else if ($ucfirst)
		{
			$this->word = ucfirst(strtolower($this->word));
		}
		else if ($upper_case)
		{
			$this->word = strtoupper($this->word);
		}
		return $this->word;
	}
}

?>