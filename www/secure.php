<?PHP

function xw_sanitycheck($str){
	if(strpos(str_replace("''",""," $str"),"'")!=false)
		return str_replace("'", "''", $str);
	else
		return $str;
}


function secure($str){
	// Case of an array
	if (is_array($str)) {
		foreach($str AS $id => $value) {
			$str[$id] = secure($value);
		}
	}
	else
		$str = xw_sanitycheck($str);

	return $str;
}

// Get Filter
$xweb_AI	= array_keys($_GET);
$i=0;
while($i<count($xweb_AI)) {
	$_GET[$xweb_AI[$i]]=secure($_GET[$xweb_AI[$i]]);
	$i++;
}


// Request Filter
$xweb_AI	= array_keys($_REQUEST);
$i=0;
while($i<count($xweb_AI)) {
	$_REQUEST[$xweb_AI[$i]]=secure($_REQUEST[$xweb_AI[$i]]);
	$i++;
}


// Post Filter
$xweb_AI	= array_keys($_POST);
$i=0;
while($i<count($xweb_AI)) {
	$_POST[$xweb_AI[$i]]=secure($_POST[$xweb_AI[$i]]);
	$i++;
}

// Cookie Filter 
$xweb_AI	= array_keys($_COOKIE);
$i=0;
while($i<count($xweb_AI)) {
	$_COOKIE[$xweb_AI[$i]]=secure($_COOKIE[$xweb_AI[$i]]);
	$i++;
}


function check_inject() {
$badchars = array(";", "'", "\"", "*", "DROP", "SHUTDOWN", "SELECT", "UPDATE", "DELETE", "-");
foreach($_POST as $value) {
if(in_array($value, $badchars)) { die("SQL Injection Detected\n"); }
else { 
$check = preg_split("//", $value, -1, PREG_SPLIT_OFFSET_CAPTURE);
foreach($check as $char) {
if(in_array($char, $badchars)) { die("SQL Injection Detected\n"); }
}
 }
  }
   }

function clean_var($var=NULL) {
$newvar = @preg_replace('/[^a-zA-Z0-9\_\-\.]/', '', $var);
if (@preg_match('/[^a-zA-Z0-9\_\-\.]/', $var)) { }
return $newvar;
}
?>