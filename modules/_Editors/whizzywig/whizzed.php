<?
/* get the edited HTML from your whizzywig page */
$edited = $_REQUEST['edited'];

/* create some basic HTML to wrap the page */
$cssFile = "/inc/simple.css";
$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html>
<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
$html .= "<title>Whizzywig made this page</title>\n";
$html .= "<link media='all' type='text/css' href='";
$html .= $cssFile;
$html .= "' rel='stylesheet'>\n</head>\n<body>\n";

/* throw in the edited HTML from whizzywig */
$html .= stripslashes($edited);

/* finish off the page */
$html .= "\n</body>\n</html>";

/*show the result */
echo $html;

/* You could write $html out to a file at this point */
?>