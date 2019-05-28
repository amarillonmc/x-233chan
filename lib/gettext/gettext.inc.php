<?php
/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is faketext.
 *
 * The Initial Developer of the Original Code is
 * Justin Scott <fligtar@gmail.com>.
 * Portions created by the Initial Developer are Copyright (C) 2007
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * ***** END LICENSE BLOCK ***** */

/**
 * faketext simulates the PHP gettext extension by parsing .po files and storing
 * their strings. All PHP gettext functions are defined to prevent warnings.
 *
 * http://blog.fligtar.com/2007/05/11/getting-text-without-gettext/
 *
 * You may need to alter bindtextdomain() to set $faketext_lang to the locale
 * you wish to display. For example, if your locale set with script.php?lang=en-US
 * you could set $faketext_lang = $_GET['lang'];
 */


$faketext = array();

/**
 * Alias of gettext()
 */
 if (!extension_loaded('gettext')) {
	function _($message) {
		return _gettext($message);
	}
 }

 /**
 * Sets a requested locale, if needed emulates it.
 */
function _setlocale($category, $locale) {
	global $CURRENTLOCALE, $EMULATEGETTEXT;
	if ($locale === 0) { // use === to differentiate between string "0"
		if ($CURRENTLOCALE != '')
			return $CURRENTLOCALE;
		else
			// obey LANG variable, maybe extend to support all of LC_* vars
			// even if we tried to read locale without setting it first
			return _setlocale($category, $CURRENTLOCALE);
	} else {
		$ret = 0;
		if (function_exists('setlocale')) // I don't know if this ever happens ;)
		$ret = setlocale(LC_ALL, $locale);
		if (($ret and $locale == '') or ($ret == $locale)) {
			$EMULATEGETTEXT = 0;
			$CURRENTLOCALE = $ret;
		} else {
		if ($locale == '') // emulate variable support
			$CURRENTLOCALE = getenv('LANG');
		else
			$CURRENTLOCALE = $locale;
			$EMULATEGETTEXT = 1;
		}
		return $CURRENTLOCALE;
	}
}

/**
 * gettext: Specifies the character encoding messages should be returned with
 * faketext: Does nothing
 */
function _bind_textdomain_codeset($domain, $codeset) {
}

/**
 * gettext: Sets the path for domain
 * faketext: Parses and stores the strings from the .po file
 */
function _bindtextdomain($domain, $directory, $faketext_lang = KU_LOCALE) {
	global $faketext;


	// Path to .po file
	$po = "{$directory}/{$faketext_lang}/LC_MESSAGES/{$domain}.po";

	if (file_exists($po)) {
		$contents = file_get_contents($po);
	}
	else {
		// If the .po wasn't found, try replacing dashes with underscores in locale
		$formatted_lang = str_replace('-', '_', $faketext_lang);
		$po = "{$directory}/{$formatted_lang}/LC_MESSAGES/{$domain}.po";
		if (file_exists($po)) {
			$contents = file_get_contents($po);
		}
		else {
			// .po not found, return
			return false;
		}
	}

	// Remove header information;
	$contents = substr($contents, strpos($contents, "\n\n"));

	// Un-escape quotes
	$contents = str_replace('\"', '"', $contents);

	// Parse strings
	preg_match_all('/msgid\s+"([^"]+)"\s*(msgid_plural\s+"([^"]+)"\s*)?((msgstr(\[\d+\])?\s+?"([^"]+)"\s*)+)/is', $contents, $localeMatches, PREG_SET_ORDER);

	// Make pretty key => value array
	foreach ($localeMatches as $localeMatch) {
		// Determine if this is a plural entry
		if (strpos($localeMatch[2], 'msgid_plural') !== false) {
			// If plural, parse each string
			$plurals = array();
			preg_match_all('/msgstr(\[\d+\])?\s+?"(.+?)"\s*/is', $localeMatch[4], $pluralMatches, PREG_SET_ORDER);

			foreach ($pluralMatches as $pluralMatch) {
				$plurals[] = str_replace("\"\n\"", '', $pluralMatch[2]);
			}

			$faketext[$localeMatch[1]] = $plurals;
		}
		else {
			$faketext[$localeMatch[1]] = str_replace("\"\n\"", '', $localeMatch[7]);
		}
	}
}

/**
 * gettext: Overrides the domain for a single lookup
 * faketext: nothing
 */
function _dcgettext($domain, $message, $category) {
}

/**
 * gettext: Plural version of dcgettext
 * faketext: nothing
 */
function _dcngettext($domain, $msgid1, $msgid2, $n, $category) {
}

/**
 * gettext: Overrides the current domain
 * faketext: nothing
 */
function _dgettext($domain, $message) {
}

/**
 * gettext: Plural version of dgettext
 * faketext: nothing
 */
function _dngettext($domain, $msgid1, $msgid2, $n) {
}

/**
 * gettext: Looks up message in current domain
 * faketext: same
 */
function _gettext($message) {
	global $faketext, $CURRENTLOCALE;

	//Ugly hack, if our locale is english, remove $faketext so strings don't get translated
	if ($CURRENTLOCALE == 'en' && !empty($faketext)) {
		unset($faketext);
	}

	return (!empty($faketext[$message]) ? $faketext[$message] : $message);
}

/**
 * gettext: Plural version of gettext
 * faketext: Same as singular version
 */
function _ngettext($msgid1, $msgid2, $n) {
	global $faketext;

	if ($n == 1) {
		return (!empty($faketext[$msgid1][0]) ? $faketext[$msgid1][0] : $msgid1);
	}
	else {
		return (!empty($faketext[$msgid1][1]) ? $faketext[$msgid1][1] : $msgid1);
	}
}

/**
 * gettext: Sets default domain
 * faketext: nothing
 */
function _textdomain($text_domain) {
}


?>
