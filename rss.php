<? // NO WHITESPACE ABOVE THIS POINT

// CREATE RSS FEED XML

include 'arrays.php';
include 'functions.php';

global $rating_text;

// Get latest 10 reviews (0-9)    
$result = db_query("SELECT reviews.revid, title, reviewer, rating, reviews.bookid, DATE_FORMAT(posted_date,'%a, %d %b %Y %T') as date FROM books, reviews WHERE reviews.bookid=books.bookid and reviews.active=1 ORDER BY reviewed_date desc, posted_date desc limit 0,10;")
  or die("the rss feed knows the cake is a lie");

// XML headers and channel tags
$output = "<?xml version=\"1.0\"?><rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\"><channel><title>GLBT Fantasy Fiction Resources</title><atom:link href=\"http://www.glbtfantasy.com/rss.php\" rel=\"self\" type=\"application/rss+xml\" /><link>http://www.glbtfantasy.com/</link><description>Latest fantasy and science-fiction book reviews.</description><language>en-us</language><copyright>Copyright 2009 GLBT Fantasy Fiction Resources</copyright><generator>Custom PHP &amp; MySQL by Finder</generator>";

// Add in item tags
while ($row = mysql_fetch_array($result)) {
	$revid = htmlspecialchars(utf8_encode(html_entity_decode($row["revid"])));
	$title = $row["title"];
	$reviewer = $row["reviewer"];
	$rating = $rating_text[$row["rating"]];
	$bookid = $row["bookid"];
	$rawdate = $row["date"];
	$date = $rawdate . " " . date('T');
	$authors = htmlspecialchars(utf8_encode(html_entity_decode(get_byline(get_authors($bookid)))));
	$link = "http://www.glbtfantasy.com/?section=review&amp;sub=$revid";

	$output .= "<item><title>$title $authors</title><link>$link</link><description>$rating by $reviewer</description><pubDate>$date</pubDate><guid>$link</guid></item>";
}

$output .= "</channel></rss>";

// Header must come first!
header("Content-Type: application/rss+xml");
echo $output;
?>