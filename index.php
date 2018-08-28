<?php 

// REGISTER GLOBALS IS OFF
$section = $_GET['section'];
$sub = $_GET['sub'];
    
include 'functions.php';
include 'arrays.php';

// THIS FUNCTION CALL MUST BE AT THE ABSOLUTE TOP OF THE DOCUMENT
custom_headers();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html lang="en-us">
<head>

<!-- JAVASCRIPT LIBRARY -->
<script type="text/javascript" src="finder.js"></script>

<!-- META INFO -->
<META name="description" content="GLBT Fantasy Fiction Resources: Reviews, news, essays, reading lists, and links for fans of fantasy and science-fiction books with gay, lesbian, bisexual, and transgendered protagonists.">

<META name="keywords" content="book reviews, gay, lesbian, bisexual, transgendered, GLBT, LGBT, fantasy, science fiction, sci-fi">

<META name="robots" content="index,follow">

<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="start" type="text/html" href="http://www.glbtfantasy.com/" title="GLBT Fantasy Fiction Resources">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="struct.css">
<link rel="stylesheet" type="text/css" href="style.css">

<!-- BASE URL -->
<base href="http://www.glbtfantasy.com">

<?php glbt_main(); ?>

<!-- TITLE -->
<title><?php echo $page_title; ?></title>
</head>

<body>

<!-- BIGDIV -->
<div id="bigdiv">

<!-- MASTHEAD -->
<h1><a name="top">GLBT Fantasy Fiction Resources</a></h1>

<!-- SKIP TO CONTENT LINK -->
<a class="skiplink" href="#startcontent">Skip over navigation</a>

<!-- TOP NAVIGATION -->
<div id="nav">
<?php include 'nav.txt'; ?>
</div>

<!-- SIDEBAR -->
<div id="right">
<?php include 'search-nav.txt'; ?>
<?php echo $sidebar_content; ?>
</div>

<!-- MAIN -->
<div id="main">
<a name="startcontent"></a>
<?php echo $main_content; ?>
<div id="spacer"></div>
</div>

<!-- BOTTOM NAVIGATION -->
<div id="bnav">
<?php include 'bnav.txt'; ?>
</div>

<!-- FOOTER -->
<div id="footer">
<?php include 'footer.txt'; ?>
</div>

<!-- BIGDIV -->
</div>

</body>
</html>
