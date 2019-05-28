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
 * +------------------------------------------------------------------------------+
 * SVG parsing class
 * +------------------------------------------------------------------------------+
 */
class Svg {
	var $width = 0;
	var $height = 0;
	var $id = '';
	var $version = '';
	var $namespaces;
	var $docname = '';
	var $docbase = '';
	var $sodipodiversion = '';
	var $inkscapeversion = '';
	var $output_extension = '';


	function Svg($svg){
		$xml = new SimpleXMLElement($svg,1,1);
		$this->width = (string) $xml['width'];
		$this->height = (string) $xml['height'];
		$this->id = (string) $xml['id'];
		$this->version = (string) $xml['version'];
		$this->namespaces = $xml->getNamespaces();
		$sodipodi = $xml->attributes($this->namespaces['sodipodi']);
		$this->docname = (string) $sodipodi['docname'];
		$this->docbase = (string) $sodipodi['docbase'];
		$this->sodipodiversion = (string) $sodipodi['version'];
		$inkscape = $xml->attributes($this->namespaces['inkscape']);
		$this->inkscapeversion = (string) $inkscape['version'];
		$this->output_extension = (string) $inkscape['ouput_extension'];
	}
}

?>