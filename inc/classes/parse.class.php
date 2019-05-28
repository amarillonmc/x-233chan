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
* A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* kusaba; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
* +------------------------------------------------------------------------------+
* Parse class
* +------------------------------------------------------------------------------+
* A post's message text will be passed, which will then be formatted and cleaned
* before being returned.
* +------------------------------------------------------------------------------+
*/
class Parse {
	var $boardtype;
	var $parentid;
	var $id;
	var $boardid;
	
	function MakeClickable($txt) {
		/* Make http:// urls in posts clickable */
		$txt = preg_replace('#(http://|https://|ftp://)([^(\s<|\[)]*)#', '<a href="\\1\\2">\\1\\2</a>', $txt);
		
		return $txt;
	} 
	
	function BBCode($string){
		$patterns = array(
			'`\[b\](.+?)\[/b\]`is', 
			'`\[i\](.+?)\[/i\]`is', 
			'`\[u\](.+?)\[/u\]`is', 
			'`\[s\](.+?)\[/s\]`is', 
			'`\[aa\](.+?)\[/aa\]`is', 
			'`\[spoiler\](.+?)\[/spoiler\]`is', 
			);
		$replaces =  array(
			'<b>\\1</b>', 
			'<i>\\1</i>', 
			'<span style="border-bottom: 1px solid">\\1</span>', 
			'<strike>\\1</strike>', 
			'<div style="font-family: Mona,\'MS PGothic\' !important;">\\1</div>', 
			'<span class="spoiler" onmouseover="this.style.color=\'white\';" onmouseout="this.style.color=\'black\'">\\1</span>', 
			);
		$string = preg_replace($patterns, $replaces , $string);
		$string = preg_replace_callback('`\[code\](.+?)\[/code\]`is', array(&$this, 'code_callback'), $string);
		
		return $string;
	}
	
	function code_callback($matches) {
		$return = '<div style="white-space: pre !important;font-family: monospace !important;">'
		. str_replace('<br />', '', $matches[1]) .
		'</div>';
		
		return $return;
	}
	
	function ColoredQuote($buffer, $boardtype) {
		/* Add a \n to keep regular expressions happy */
		if (substr($buffer, -1, 1)!="\n") {
			$buffer .= "\n";
		}
	
		if ($boardtype==1) {
			/* The css for text boards use 'quote' as the class for quotes */
			$class = 'quote';
			$linechar = '';
		} else {
			/* The css for imageboards use 'unkfunc' (???) as the class for quotes */
			$class = 'unkfunc';
			$linechar = "\n";
		}
		$buffer = preg_replace('/^(&gt;[^>](.*))\n/m', '<span class="'.$class.'">\\1</span>' . $linechar, $buffer);
		/* Remove the > from the quoted line if it is a text board */
		if ($boardtype==1) {
			$buffer = str_replace('<span class="'.$class.'">&gt;', '<span class="'.$class.'">', $buffer);
		}
	
		return $buffer;
	}
	
	function ClickableQuote($buffer, $board, $boardtype, $parentid, $boardid, $ispage = false) {
		global $thread_board_return;
		$thread_board_return = $board;
		$thread_board_id = $boardid;
		
		/* Add html for links to posts in the board the post was made */
		$buffer = preg_replace_callback('/&gt;&gt;([r]?[l]?[f]?[q]?[0-9,\-,\,]+)/', array(&$this, 'InterthreadQuoteCheck'), $buffer);
		
		/* Add html for links to posts made in a different board */
		$buffer = preg_replace_callback('/&gt;&gt;\/([a-z]+)\/([0-9]+)/', array(&$this, 'InterboardQuoteCheck'), $buffer);
		
		return $buffer;
	}
	
	function InterthreadQuoteCheck($matches) {
		global $tc_db, $ispage, $thread_board_return, $thread_board_id;

		$lastchar = '';
		// If the quote ends with a , or -, cut it off.
		if(substr($matches[0], -1) == "," || substr($matches[0], -1) == "-") {
			$lastchar = substr($matches[0], -1);
			$matches[1] = substr($matches[1], 0, -1);
			$matches[0] = substr($matches[0], 0, -1);
		}
		if ($this->boardtype != 1 && is_numeric($matches[1])) {

			$query = "SELECT `parentid` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $this->boardid . " AND `id` = ".$tc_db->qstr($matches[1]);
			$result = $tc_db->GetOne($query);
			if ($result !== '') {
				if ($result == 0) {
					$realid = $matches[1];
				} else {
					$realid = $result;
				}
			} else {
				return $matches[0];
			}
			
			$return = '<a href="'.KU_BOARDSFOLDER.$thread_board_return.'/res/'.$realid.'.html#'.$matches[1].'" onclick="return highlight(\'' . $matches[1] . '\', true);" class="ref|' . $thread_board_return . '|' .$realid . '|' . $matches[1] . '">'.$matches[0].'</a>'.$lastchar;
		} else {
			$return = $matches[0];
			
			$postids = getQuoteIds($matches[1]);
			if (count($postids) > 0) {
				$realid = $this->parentid;
				if ($realid === 0) {
					if ($this->id > 0) {
						$realid = $this->id;
					}
				}
				if ($realid !== '') {
					$return = '<a href="' . KU_BOARDSFOLDER . 'read.php';
					if (KU_TRADITIONALREAD) {
						$return .= '/' . $thread_board_return . '/' . $realid.'/' . $matches[1];
					} else {
						$return .= '?b=' . $thread_board_return . '&t=' . $realid.'&p=' . $matches[1];
					}
					$return .= '">' . $matches[0] . '</a>';
				}
			}
		}
		
		return $return;
	}
	
	function InterboardQuoteCheck($matches) {
		global $tc_db;

		$result = $tc_db->GetAll("SELECT `id`, `type` FROM `".KU_DBPREFIX."boards` WHERE `name` = ".$tc_db->qstr($matches[1])."");
		if ($result[0]["type"] != '') {
			$result2 = $tc_db->GetOne("SELECT `parentid` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $result[0]['id'] . " AND `id` = ".$tc_db->qstr($matches[2])."");
			if ($result2 != '') {
				if ($result2 == 0) {
					$realid = $matches[2];
				} else {
					if ($result[0]['type'] != 1) {
						$realid = $result2;
					}
				}
				
				if ($result[0]["type"] != 1) {
					return '<a href="'.KU_BOARDSFOLDER.$matches[1].'/res/'.$realid.'.html#'.$matches[2].'" class="ref|' . $matches[1] . '|' . $realid . '|' . $matches[2] . '">'.$matches[0].'</a>';
				} else {
					return '<a href="'.KU_BOARDSFOLDER.$matches[1].'/res/'.$realid.'.html" class="ref|' . $matches[1] . '|' . $realid . '|' . $realid . '">'.$matches[0].'</a>';
				}
			}
		}
		
		return $matches[0];
	}
	
	function Wordfilter($buffer, $board) {
		global $tc_db;
		
		$query = "SELECT * FROM `".KU_DBPREFIX."wordfilter`";
		$results = $tc_db->GetAll($query);
		foreach($results AS $line) {
			$array_boards = explode('|', $line['boards']);
			if (in_array($board, $array_boards)) {
				$replace_word = $line['word'];
				$replace_replacedby = $line['replacedby'];
				
				$buffer = ($line['regex'] == 1) ? preg_replace($replace_word, $replace_replacedby, $buffer) : str_ireplace($replace_word, $replace_replacedby, $buffer);
			}
		}
		
		return $buffer;
	}
	
	function CheckNotEmpty($buffer) {
		$buffer_temp = str_replace("\n", "", $buffer);
		$buffer_temp = str_replace("<br>", "", $buffer_temp);
		$buffer_temp = str_replace("<br/>", "", $buffer_temp);
		$buffer_temp = str_replace("<br />", "", $buffer_temp);

		$buffer_temp = str_replace(" ", "", $buffer_temp);
		
		if ($buffer_temp=="") {
			return "";
		} else {
			return $buffer;
		}
	}
	
	/* From http://us.php.net/wordwrap */
	/*function CutWord($str, $maxLength, $char){
	    $wordEndChars = array(" ", "\n", "\r", "\f", "\v", "\0");
	    $count = 0;
	    $newStr = "";
	    $openTag = false;
	    for($i=0; $i<strlen($str); $i++){
	        $newStr .= $str{$i};   
			echo 'newstr: ' . $newStr . '<hr>' . "\n";
	        if($str{$i} == "<"){
	            $openTag = true;
	            continue;
	        }
	        if(($openTag) && ($str{$i} == ">")){
	            $openTag = false;
	            continue;
	        }
	       
	        if(!$openTag){
	            if(!in_array($str{$i}, $wordEndChars)){//If not word ending char
	                $count++;
	                if($count==$maxLength){//if current word max length is reached
	                    $newStr .= $char;//insert word break char
	                    $count = 0;
	                }
	            }else{//Else char is word ending, reset word char count
	                    $count = 0;
	            }
	        }
	       
	    }//End for   
	    die($newStr);
	    return $newStr;
	}*/
	
	/*function CutWord($txt, $where) {
		if (empty($txt)) return false;
		for ($c = 0, $a = 0, $g = 0; $c<strlen($txt); $c++) {
			$d[$c+$g]=$txt[$c];
			if ($txt[$c]!=' '&&$txt[$c]!=chr(10)) $a++;
			else if ($txt[$c]==' '||$txt[$c]==chr(10)) $a = 0;
			if ($a==$where) {
			$g++;
			$d[$c+$g]="\n";
			$a = 0;
			}
		}
		
		return implode("", $d);
	}*/
	
	function CutWord($txt, $where) {
		$txt_split_primary = preg_split('/\n/', $txt);
		$txt_processed = '';
		$usemb = (function_exists('mb_substr') && function_exists('mb_strlen')) ? true : false;
		
		foreach ($txt_split_primary as $txt_split) {
			$txt_split_secondary = preg_split('/ /', $txt_split);
			
			foreach ($txt_split_secondary as $txt_segment) {
				$segment_length = ($usemb) ? mb_strlen($txt_segment) : strlen($txt_segment);
				while ($segment_length > $where) {
					if ($usemb) {
						$txt_processed .= mb_substr($txt_segment, 0, $where) . "\n";
						$txt_segment = mb_substr($txt_segment, $where);
						
						$segment_length = mb_strlen($txt_segment);
					} else {
						$txt_processed .= substr($txt_segment, 0, $where) . "\n";
						$txt_segment = substr($txt_segment, $where);
						
						$segment_length = strlen($txt_segment);
					}
				}
				
				$txt_processed .= $txt_segment . ' ';
			}
			
			$txt_processed = ($usemb) ? mb_substr($txt_processed, 0, -1) : substr($txt_processed, 0, -1);
			$txt_processed .= "\n";
		}
		
		return $txt_processed;
	}
	
	function ParsePost($message, $board, $boardtype, $parentid, $boardid, $ispage = false) {
		$this->boardtype = $boardtype;
		$this->parentid = $parentid;
		$this->boardid = $boardid;
		
		$message = trim($message);
		$message = $this->CutWord($message, (KU_LINELENGTH / 15));
		$message = htmlspecialchars($message, ENT_QUOTES);
		if (KU_MAKELINKS) {
			$message = $this->MakeClickable($message);
		}
		$message = $this->ClickableQuote($message, $board, $boardtype, $parentid, $boardid, $ispage);
		$message = $this->ColoredQuote($message, $boardtype);
		/*if (KU_MARKDOWN) {
			require KU_ROOTDIR . 'lib/markdown/markdown.php';
			$message = Markdown($message);
		}*/
		$message = str_replace("\n", '<br />', $message);
		$message = $this->BBCode($message);
		$message = $this->Wordfilter($message, $board);
		$message = $this->CheckNotEmpty($message);
		
		return $message;
	}
}
?>