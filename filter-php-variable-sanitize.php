// =====================================(experiment to clean+sanitize php strings)======================= \\
// ==========================================  SEE DEMO - https://goo.gl/PNBKSj  ======================== \\


<?php
$text='a 1`-=[]\;\',./~!@#$%^&*()_+{}|:"<>?';
header('Content-Type: text/html; charset=utf-8');
$divideeeeeeeeer='<td>------------------------------------------------------------------------------------</td><td>-----------------------------------------------------------</td></tr>';
echo '<style>table { border-collapse:collapse; }  table tr {border:1px solid #f5f5f5;}  td:nth-child(2) { max-width: 600px; position:relative; top:-3px; color:green; } td { max-width: 500px; } </style>';
echo '<table><tbody>';
echo '<tr style="background-color:rgb(205, 245, 205);"><td><b>'.htmlentities($text).'</b> (used text)</td><td> (USED FUNCTION)</td></tr>';
echo $divideeeeeeeeer;
echo '<tr><td><code>'.(is_numeric($text) ? "true":"false").'</code></td><td> <code> if (is_numeric($text))</code> [returns true or false]</td></tr>';
echo '<tr><td><code>'.(preg_match('/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si',$text) ? "true":"false").'</code></td><td><code> if (preg_match(\'/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si\',$text))</code> [if string contains your determined chars: true or false]</td></tr>';
echo $divideeeeeeeeer;
echo '<tr><td><code>'.htmlentities(preg_replace('/[\`\~\!\@\#\$\%\^\&\*\(\)\-\_\+\=\{\}\[\]\}\\\|:\;\"\'\<\,\>\.\?\/]/si','',$text)).'</code></td><td> <code>preg_replace(\'/\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\{\[\}\}\\\|:\;\"\'\<\,\>\.\?\//si\',\'\',$text)</code> [removes your determined chars]</td></tr>';
echo '<tr><td><code>'.htmlentities(preg_replace('/\W/si','',$text)).'</code></td><td> <code>preg_replace(\'/\W/si\',\'\',$text)</code> [removes Non alpha-numeric chars]</td></tr>';
echo '<tr><td><code>'.htmlentities(urlencode($text)).'</code></td><td>  <code> urlencode($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(addslashes($text)).'</code></td><td>  <code> addslashes($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(stripslashes($text)).'</code></td><td> <code>  stripslashes($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(strip_tags($text)).'</code></td><td>  <code> strip_tags($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(htmlspecialchars($text)).'</code></td><td>  <code> htmlspecialchars($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(htmlentities($text)).'</code></td><td>  <code> htmlentities($text)</code> [<b>htmlentities</b> has different parameters]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_COMPAT)</code> [convert double-quotes and leave single-quotes alone]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_QUOTES)</code> [convert both double and single quotes]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_NOQUOTES)</code> [convert double-quotes and leave single-quotes alone]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_IGNORE) </code> [Silently discard invalid code unit sequences instead of returning an empty string, <a href="http://unicode.org/reports/tr36/#Deletion_of_Noncharacters">may have security threats</a>. ]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_SUBSTITUTE) [Replace invalid code unit sequences with a Unicod.....]</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_DISALLOWED) [Replace invalid code points for the given document type with a Unicode Replacement Character....]</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_HTML401)</code> [Handle code as HTML 4.01 ]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_XML1)</code> [Handle code as XML 1 ]</td></tr>';
echo '<tr><td><code>same as above</code></td><td>  <code> htmlentities($text,ENT_XHTML)</code> [Handle code as XHTML ]</td></tr>';
echo $divideeeeeeeeer;
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_EMAIL)).'</code></td><td><code> filter_var($text, FILTER_SANITIZE_EMAIL)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_ENCODED)).'</code></td><td><code> filter_var($text, FILTER_SANITIZE_ENCODED)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code> filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_HIGH)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_MAGIC_QUOTES)).'</code></td><td><code>  filter_var($text, FILTER_SANITIZE_MAGIC_QUOTES)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_NUMBER_INT)).'</code></td><td><code>  filter_var($text, FILTER_SANITIZE_NUMBER_INT)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS)).'</code></td><td><code>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td> <code> filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS )</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td> <code> filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_STRING)).'</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td> <code> filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_STRIPPED)).'</code></td><td> <code> filter_var($text, FILTER_SANITIZE_STRIPPED)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_SANITIZE_URL)).'</code></td><td><code>  filter_var($text, FILTER_SANITIZE_URL)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(filter_var($text, FILTER_UNSAFE_RAW)).'</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_HIGH)</code></td></tr>';
echo '<tr><td><code>same as above</code></td><td><code>  filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_AMP)</code></td></tr>';
if(function_exists('sanitize_title')){ 
echo $divideeeeeeeeer;
echo '<tr><td><h3 style="color:red;">for WordPress bulit-in functions</h3><td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_title($text)).'</code></td><td><code>sanitize_title($text)</code></td></tr>';
//echo '<tr><td><code>'.(sanitize_title_with_dashes($text)).'</code></td><td><code>sanitize_title_with_dashes($text)</code></td></tr>';
//echo '<tr><td><code>'.(sanitize_title_for_query($text)).'</code></td><td><code>sanitize_title_for_query($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_text_field($text)).'</code></td><td><code>sanitize_text_field($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_key($text)).'</code></td><td><code>sanitize_key($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_html_class($text)).'</code></td><td><code>sanitize_html_class($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_file_name($text)).'</code></td><td><code>sanitize_file_name($text)</code></td></tr>';
echo '<tr><td><code>'.htmlentities(sanitize_email($text)).'</code></td><td><code>sanitize_email($text)</code></td></tr>';
}
echo $divideeeeeeeeer;
echo '<tr><td cospan="2">source code at : <b>github.com/tazotodua</b><td></tr>';
echo '</tbody></table>';
?>
