=====================================(experiment to clean+sanitize php strings)=======================
SEE DEMO - http://i.stack.imgur.com/ZAJBL.png





<?php
$text='@!#$%&\'*+-\\/=?^_`{|}~.[]123céf<ag>';


header('Content-Type: text/html; charset=utf-8');


$divideeeeeeeeer='<td>------------------------------------------------------------------------------------</td><td>-----------------------------------------------------------</td></tr>';
echo '<table><tbody>';
echo '<tr style="background-color:rgb(205, 245, 205);"><td> &nbsp;string &nbsp;<b>'.htmlentities($text).'</b> (initial)</td><td> (USED FUNCTIONS COLUMN)</td></tr>';
echo $divideeeeeeeeer;

echo '<tr><td>';var_dump(is_numeric($text)); echo '</td><td> <code> if (is_numeric($text))</code> [returns true or false]</td></tr>';
echo '<tr><td>';var_dump(strlen($text) > 11); echo '</td><td><code>  if (strlen($text)) &gt; 11)</code> [returns true or false]</td></tr>';
echo '<tr><td>';var_dump(preg_match('/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si',$text)); echo '</td><td><code> if (preg_match(\'/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si\',$text))</code> [if string contains your determined chars: true or false]</td></tr>';
echo $divideeeeeeeeer;
echo '<tr><td>';var_dump(preg_replace('/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si','',$text)); echo '</td><td> <code>preg_replace(\'/\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\{\[\}\}\\\|:\;\"\'\<\,\>\.\?\//si\',\'\',$text)</code> [removes your determined chars]</td></tr>';
echo '<tr><td>';var_dump(preg_replace('/\W/si','',$text)); echo '</td><td> <code>preg_replace(\'/\W/si\',\'\',$text)</code> [removes Non alpha-numeric chars]</td></tr>';
echo '<tr><td>';var_dump(urlencode($text)); echo '</td><td>  <code> urlencode($text)</code></td></tr>';
echo '<tr><td>';var_dump(addslashes($text)); echo '</td><td>  <code> addslashes($text)</code></td></tr>';
echo '<tr><td>';var_dump(stripslashes($text)); echo '</td><td> <code>  stripslashes($text)</code></td></tr>';
echo '<tr><td>';var_dump(strip_tags($text)); echo '</td><td>  <code> strip_tags($text)</code></td></tr>';
echo '<tr><td>';var_dump(htmlspecialchars($text)); echo '</td><td>  <code> htmlspecialchars($text)</code></td></tr>';
echo '<tr><td>';var_dump(htmlentities($text)); echo '</td><td>  <code> htmlentities($text)</code> [<b>htmlentities</b> has different parameters]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_COMPAT)); echo '</td><td>  <code> htmlentities($text,ENT_COMPAT)</code> [convert double-quotes and leave single-quotes alone]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_QUOTES)); echo '</td><td>  <code> htmlentities($text,ENT_QUOTES)</code> [convert both double and single quotes]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_NOQUOTES)); echo '</td><td>  <code> htmlentities($text,ENT_NOQUOTES)</code> [convert double-quotes and leave single-quotes alone]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_IGNORE)); echo '</td><td>  <code> htmlentities($text,ENT_IGNORE) </code> [Silently discard invalid code unit sequences instead of returning an empty string. Using this flag is discouraged as it <a href="http://unicode.org/reports/tr36/#Deletion_of_Noncharacters">may have security implications</a>. ]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_SUBSTITUTE)); echo '</td><td>  <code> htmlentities($text,ENT_SUBSTITUTE) [Replace invalid code unit sequences with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of returning an empty string]</code></td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_DISALLOWED)); echo '</td><td>  <code> htmlentities($text,ENT_DISALLOWED) [Replace invalid code points for the given document type with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of leaving them as is. This may be useful, for instance, to ensure the well-formedness of XML documents with embedded external content. ]</code></td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_HTML401)); echo '</td><td>  <code> htmlentities($text,ENT_HTML401)</code> [Handle code as HTML 4.01 ]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_XML1)); echo '</td><td>  <code> htmlentities($text,ENT_XML1)</code> [Handle code as XML 1 ]</td></tr>';
echo '<tr><td>';var_dump(htmlentities($text,ENT_XHTML)); echo '</td><td>  <code> htmlentities($text,ENT_XHTML)</code> [Handle code as XHTML ]</td></tr>';
echo $divideeeeeeeeer;
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_EMAIL));echo '</td><td> filter_var($text, FILTER_SANITIZE_EMAIL)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_ENCODED));echo '</td><td> filter_var($text, FILTER_SANITIZE_ENCODED)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW));echo '</td><td> filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_LOW));echo '</td><td>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_MAGIC_QUOTES));echo '</td><td>  filter_var($text, FILTER_SANITIZE_MAGIC_QUOTES)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_NUMBER_INT));echo '</td><td>  filter_var($text, FILTER_SANITIZE_NUMBER_INT)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS));echo '</td><td>  filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS )</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING));echo '</td><td>  filter_var($text, FILTER_SANITIZE_STRING)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP));echo '</td><td>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_AMP)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_STRIPPED));echo '</td><td>  filter_var($text, FILTER_SANITIZE_STRIPPED)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_SANITIZE_URL));echo '</td><td>  filter_var($text, FILTER_SANITIZE_URL)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_HIGH));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_HIGH)</td></tr>';
echo '<tr><td>';var_dump(filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_AMP));echo '</td><td>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_AMP)</td></tr>';
echo '</tbody></table>';
?>
