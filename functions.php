<?php

// GLBT MAIN
function glbt_main() {
	
	// Inputs
	global $section;
	global $sub;
	
	$link = mysql_connect("xxxx", "xxxx", "xxxx")
    or die("Could not connect");
	
    $raw_sub = $sub;
    
	$section = mysql_real_escape_string($section, $link);
	$sub = mysql_real_escape_string($sub, $link);
	
	// Arrays
	global $title_section;
	global $title_sub;
	global $reviewer_names;
	
	// Variable assignments
	global $page_title;
	global $main_content;
	global $sidebar_content;
	
	// Default page title
	$page_title = "GLBT Fantasy Fiction Resources | " . $title_section[$section];
	
	// HOMEPAGE
    if ($section == "") {
	    // Blank out sub variable for safety
	    $sub = "";
	    // Page title needs to be reset
	    $page_title = "GLBT Fantasy Fiction Resources";

        $main_content = get_focus_review();
        $main_content .= "<div id='news'>" . file_get_contents('news-latest.txt') . "</div>";

	    $sidebar_content = get_latest_reviews();
        $sidebar_content .= file_get_contents('home-notes.txt');
    }
    // AUTHOR INDEX
    elseif ($section == "index") {
	    // Blank out sub variable for safety
	    $sub = "";
	    // Page title is default
        $main_content = get_author_index();
        $sidebar_content = get_reviews_by_reviewer_index();
    }
    // SINGLE AUTHOR
    elseif ($section == "author") {
	    $sidebar_content = "<h2>More Info</h2>\n";
	    $sidebar_content .= "<ul>\n<li><a href='?section=index'>Book Reviews: by Author</a></li>\n";

	    if (is_numeric($sub)) {
		    
			$review_list = get_single_author_reviews($sub);
			
			if ($review_list == "") {
            	$main_content = "<p>Error: No reviews found for this author.</p>\n\n";
            	$page_title .= "Error";
        	}
            else {
	            $page_title .= get_author_name($sub);
	            $main_content = $review_list;
	            
	            $homepage = get_author_homepage($sub);
	            //$interview = get_author_interview($sub);
	            
	            //if (($homepage != "") || ($interview != "")) {
		        
		        if ($homepage) {
			        $sidebar_content .= $homepage;
		            
		            //$sidebar_content .= $homepage . $interview;
	            }
            }
        }
        // No author specified
        else {
	        $page_title .= "Error";
	        $main_content = "<p>Error: Must specify an author number.\n\n";
        }
        $sidebar_content .= "</ul>\n\n";
        $sidebar_content .= get_reviews_by_reviewer_index();
    }
    // SINGLE REVIEW
    elseif ($section == "review") {
	    if (is_numeric($sub)) {
		    $review_content = get_single_review($sub);
		    
		    if ($review_content == "") {
			    $page_title .= "Error";
			    $main_content = "<p>Error: No such review.</p>\n\n";
			    $sidebar_content = get_reviews_by_reviewer_index();
		    }
		    else {
			    $page_title .= get_review_page_title($sub);
			    $main_content = $review_content;
			    
			    $book = get_book_from_revid($sub);
			    
			    $sidebar_content .= "\n\n<h2>More Info</h2>\n";
			    $sidebar_content .= "<ul>\n";
			    $sidebar_content .= "<li>" . get_genre($book) . ".</li>\n";
			    $sidebar_content .= "<li>Published in " . get_pub_date($book) . ".</li>\n";
			    $sidebar_content .= "<li>ISBN: " . get_isbn($book) . ".</li>\n";
			    
			    $sidebar_content .= "\n\n<h2>Links</h2>\n";
			    $authors = get_authors($book);
			    // Author homepage links
			    foreach ($authors as $author) {
				    $sidebar_content .= get_author_homepage($author["authid"]);
			    }
			    // Author interview links
			    //foreach ($authors as $author) {
				    //$sidebar_content .= get_author_interview($author["authid"]);
			    //}
			    // Author page links
			    foreach ($authors as $author) {
				    $authid = $author["authid"];
				    $name = $author["full_name"];
				    $sidebar_content .= "<li><a href='?section=author&amp;sub=$authid'>More reviews of<br>$name</a></li>\n";
			    }
			    // Reviewer page link
			    $reviewer = get_reviewer($sub);
			    $sidebar_content .= "<li><a href='?section=reviewer&amp;sub=" . strtolower($reviewer) . "'>More reviews by $reviewer</a></li>\n";
			    $sidebar_content .= "</ul>\n";
			    
			    // More reviews of this book
			    $more_reviews = get_more_reviews_by_revid($sub);
			    if ($more_reviews) {
				    $sidebar_content .= "\n<h2 style='line-height: 140%'>More reviews<br>of this book</h2>\n";
				    $sidebar_content .= "<ul>\n" . $more_reviews . "</ul>\n";
			    }
			}
		}
		// No review specified
		else {
			$page_title .= "Error";
		    $main_content = "<p>Error: Must specify a review number.</p>\n\n";
		    $sidebar_content = get_reviews_by_reviewer_index();
	    }
    }
    // SINGLE REVIEWER
    elseif ($section == "reviewer") {
	    if ($sub) {
		    $reviews = get_single_reviewer_reviews($sub);
		    
		    if ($reviews == "") {
			    $page_title .= "Error";
			    $main_content = "<p>Error: No such reviewer.</p>\n\n";
			    $sidebar_content = get_reviews_by_reviewer_index();
		    }
		    else {
			    $page_title .= " | " . $reviewer_names[$sub];
			    $main_content = $reviews;
			    
			    $sidebar_content = "<h2>More Info</h2>\n";
			    $sidebar_content .= "<ul>\n<li><a href='?section=about&amp;sub=" . strtolower($sub) . "'>About " . $reviewer_names[$sub] . "</a></li>\n</ul>\n\n";
			    $sidebar_content .= get_reviews_by_reviewer_index();
		    }
	    }
	    // No reviewer specified
	    else {
	        $page_title .= "Error";
	        $main_content = "<p>Error: Must specify a valid reviewer.\n\n";
	        $sidebar_content = get_reviews_by_reviewer_index();
	    }
    }
    // SEARCH
    elseif ($section == "search") {
        // Page title is default
        // $main_content = "<h2>Search Results</h2>\n\n";
        $main_content .= search_for($sub, $raw_sub);
        $sidebar_content = file_get_contents('search-more.txt');
    }
    // GUIDELINES
    elseif ($section == "guidelines") {
	    // Blank out sub variable
	    $sub = "";
	    // Page title is default
	    $main_content = file_get_contents('guidelines.txt');
	    $sidebar_content = file_get_contents('guidelines-more.txt');
    }
    // ESSAYS, INTERVIEWS, READING LISTS, LINKS, NEWS
    //elseif (in_array($section, array(1 => "essays", 2 => "interviews", 3 => "lists", 4 => "links", 5 => "news"))) {
    elseif (in_array($section, array(1 => "essays", 2 => "lists", 3 => "links", 4 => "news"))) {
	    
	    //$what = array("essays" => "essay", "interviews" => "interview", "lists" => "list", "links" => "links", "news" => "news");
	    $what = array("essays" => "essay", "lists" => "list", "links" => "links", "news" => "news");
	    $which = $what[$section];
	    
	    // Sidebar is always the same
        $sidebar_file = $which . "-index-more.txt";
        $sidebar_content = file_get_contents($sidebar_file);
        
        if ($sub) {
	        if (array_key_exists($sub, $title_sub)) {
		        $main_file = ($which . "-" . $sub . ".txt");
		        if (file_exists($main_file)) {
			        $page_title .=  " | " . $title_sub[$sub];
		            $main_content = file_get_contents($main_file);
	            }
	            // Include file not found.
	            else {
		            $page_title .= " | Error";
		            $main_content = "<p>Error: Missing include file.</p>\n\n";
	            }
            }
            // No such sub
            else {
	            $page_title .= " | Error";
	            $main_content = "<p>Error: No such $which.</p>\n\n";
            }
        }
        // Main index, no sub
        else {
	        // Page title is default
	        $main_file =  $which . "-index.txt";
	        $main_content = file_get_contents($main_file);
         }
     }
     // ABOUT
     elseif ($section == "about") {
	     
	     if ($sub) {
		     if (array_key_exists($sub, $reviewer_names)) {
			     $main_file = "about-" . $sub . ".txt";
			     if (file_exists($main_file)) {
   				     $capname = $reviewer_names[$sub];
				     $page_title .= " | " . $capname;
				     $main_content = file_get_contents($main_file);
				     // Forget custom sidebar file for the moment!
				     // $sidebar_file = "about-" . $sub . "-more.txt";
				     // $sidebar_content = file_get_contents($sidebar_file);
				     	     
				     $sidebar_content .= "<h2>Live Statistics</h2>\n";
				     $sidebar_content .= get_reviewer_statistics($sub);
				     $sidebar_content .= "<h2>Latest Review</h2>\n";
				     $sidebar_content .= get_last_review($capname);
				     $sidebar_content .= "<p><em><a href='?section=reviewer&amp;sub=$sub'>See all reviews</a></em></p>\n";
				     $sidebar_content .= get_about_reviewer_index();
			     }
			     // Include file not found.
			     else {
				     $page_title .= "Error";
				     $main_content = "<p>Error: Missing include file.\n\n";
				     $sidebar_content = get_about_reviewer_index() . file_get_contents('about-more.txt');
			     }
		     }
		     // No such reviewer
	         else {
		         $page_title .= "Error";
		         $main_content = "<p>Error: No such reviewer.\n\n";
		         $sidebar_content = get_about_reviewer_index() . file_get_contents('about-more.txt');
	         }
         }
         // About the website, no sub
         else {
	         // Page title is default
	         $main_content = file_get_contents('about.txt');
	         $sidebar_content = get_website_statistics() . get_about_reviewer_index() . file_get_contents('about-more.txt');
         }
     }
}


// COLORIZE
function colorize($rating) {
	
	global $rating_text;
	global $rating_colors;
	
	$color = $rating_colors[$rating];
    $rating_display = "<span class='$color'>$rating_text[$rating]</span>";
    
    return $rating_display;
}

	
// CUSTOM HEADERS & 404 Not Found
function custom_headers() {
	
	global $section_array;
	global $section;
	
	// Last number is cachability in days.
	// HTTP IF MODIFIED SINCE might break when register globals is turned off
  
    $last_mod_time = filemtime('00glbtmod.txt');
    $last_mod_gmt = gmdate('D, d M Y H:i:s', $last_mod_time) . ' GMT';
    $if_mod_since = isset($HTTP_IF_MODIFIED_SINCE) ? $HTTP_IF_MODIFIED_SINCE : 0;
    $if_mod_time = strtotime($if_mod_since);

    if (($section) && (in_array($section, $section_array)==0)) {	    
	    header("HTTP/1.0 404 Not Found");
	    include 'glbt404.html';
	    exit;
    }
    elseif ($if_mod_since and $if_mod_time >= $last_mod_time) {
	    header("HTTP/1.0 304 Not Modified");
        exit;
    }
    else {
	    header("Last-Modified: $last_mod_gmt");
        header("Cache-Control: must-revalidate");

        $offset = 60 * 60 * 24 * 1;
        $expires_gmt = gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header("Expires: $expires_gmt");
    }
}


// DB QUERY
function db_query($query) {
	
	$link = mysql_connect("xxxx", "xxxx", "xxxx")
      or die("Could not connect");
    
    mysql_select_db("glbt_test")
      or die("Could not select database");
      
    return mysql_query($query);
}


// DISPLAY LAST MODIFIED
function display_last_modified($file) {
	
	$raw = filemtime($file);
	$form_date = date("F j, Y", $raw);
	
    echo "Last Website Update: " . $form_date . ".\n";
	
}


// GET ABOUT REVIEWER INDEX
function get_about_reviewer_index() {
	
	$reviewer_index = "";
	
	global $reviewer_names;
	
	$reviewer_index .= "<h2>About Our Reviewers</h2>\n\n";
	foreach ($reviewer_names as $name => $value) {
		$reviewer_index .= "<a href='?section=about&amp;sub=$name'>$value</a> : \n";
	}
	$reviewer_index = rtrim($reviewer_index, " : \n");
	$reviewer_index .= "\n\n";
	return $reviewer_index;
}


// GET AUTHOR HOMEPAGE
function get_author_homepage($authid) {
	$result = db_query("SELECT url, full_name FROM authors WHERE authid=$authid")
		or die("authid-query crapped itself");
	
	$row = mysql_fetch_array($result);
	$url = $row['url'];
	$full_name = $row['full_name'];
	
	mysql_free_result($result);
	if ($url == "") {
		$link = "";
	}
	else {
		$link = "<li><a class='offsite' href='$url'>$full_name's Homepage</a></li>\n";
	}
	return $link;
}


// GET AUTHOR INDEX
function get_author_index() {
	
	$author_index = "";
	
  // All authors + pen names will be alphabetized together
  $result = db_query("(SELECT authid, pen_name as full_name, pen_last_name as last_name FROM by_author WHERE pen_name<>'') UNION (SELECT authid, full_name, last_name FROM authors WHERE full_name<>'') ORDER BY last_name asc")
    or die("display_author_index just spontaneously combusted");

    $num_authors = mysql_num_rows($result);
    
    // Four columns; 1-3 left over authors divided between first two columns
    $authors_per_column = floor($num_authors / 4);
    $extra = ($num_authors % 4);
    
    $first_count = ($extra > 0) ? ($authors_per_column + 1) : $authors_per_column;
    $second_count = ($extra > 1) ? ($authors_per_column + 1) : $authors_per_column;
    $third_count = ($extra == 3) ? ($authors_per_column + 1) : $authors_per_column;
    $fourth_count = $authors_per_column;
    
    $letter = "";
    
    $author_index .= "<h2>Book Reviews: by Author</h2>\n\n";
    
    // First column
    $author_index .= "<div class='auth'>\n";
    for ($x=0; $x < $first_count; $x++) {
	    
	    $row = mysql_fetch_array($result);
	    $authid = $row["authid"];
	    $last_name = $row["last_name"];
	    $full_name = $row["full_name"];
	    
	    $url = "?section=author&amp;sub=$authid";
	    
	    $new_letter = strtoupper(substr($last_name, 0, 1));
	    
	    if ($new_letter<>$letter) {
		    $author_index .= "<h3>$new_letter</h3>\n";
	    }
	    $author_index .= "<a href='$url'>$full_name</a><br>\n";
	    $letter = $new_letter;
    }
    $author_index .= "</div>\n";
    
    // Second column
    $author_index .= "<div class='auth'>\n";
    for ($x=0; $x < $second_count; $x++) {
	    
	    $row = mysql_fetch_array($result);
	    $authid = $row["authid"];
	    $last_name = $row["last_name"];
	    $full_name = $row["full_name"];
	    
	    $url = "?section=author&amp;sub=$authid";
	    
	    $new_letter = strtoupper(substr($last_name, 0, 1));
	    
	    if ($new_letter<>$letter) {
		    $author_index .= "<h3>$new_letter</h3>\n";
	    }
	    $author_index .= "<a href='$url'>$full_name</a><br>\n";
	    $letter = $new_letter;
    }
    $author_index .= "</div>\n";
    
    // Third column
    $author_index .= "<div class='auth'>\n";
    for ($x=0; $x < $third_count; $x++) {
	    
	    $row = mysql_fetch_array($result);
	    $authid = $row["authid"];
	    $last_name = $row["last_name"];
	    $full_name = $row["full_name"];
	    
	    $url = "?section=author&amp;sub=$authid";
	    
	    $new_letter = strtoupper(substr($last_name, 0, 1));
	    
	    if ($new_letter<>$letter) {
		    $author_index .= "<h3>$new_letter</h3>\n";
	    }
	    $author_index .= "<a href='$url'>$full_name</a><br>\n";
	    $letter = $new_letter;
    }
    $author_index .= "</div>\n";
    
    // Fourth column
    $author_index .= "<div class='auth'>\n";
    for ($x=0; $x < $fourth_count; $x++) {
	    
	    $row = mysql_fetch_array($result);
	    $authid = $row["authid"];
	    $last_name = $row["last_name"];
	    $full_name = $row["full_name"];
	    
	    $url = "?section=author&amp;sub=$authid";
	    
	    $new_letter = strtoupper(substr($last_name, 0, 1));
	    
	    if ($new_letter<>$letter) {
		    $author_index .= "<h3>$new_letter</h3>\n";
	    }
	    $author_index .= "<a href='$url'>$full_name</a><br>\n";
	    $letter = $new_letter;
    }
    $author_index .= "</div>\n";
    mysql_free_result($result);
    return $author_index;
}


// GET AUTHOR INTERVIEW
// function get_author_interview($authid) {
//	   $result = db_query("SELECT interview, full_name FROM authors WHERE authid=$authid")
//	  	or die("authid-query crapped itself");
//	
//	$row = mysql_fetch_array($result);
//	$interview = $row['interview'];
//	$full_name = $row['full_name'];
//	
//	mysql_free_result($result);
//	if ($interview == "") {
//		$link = "";
//	}
//	else {
//		$link = "<li><a href='$interview'>Our interview with<br>$full_name</a></li>\n";
//	}
//	return $link;
//}


// GET AUTHOR NAME
function get_author_name($authid) {
	$result = db_query("SELECT full_name FROM authors WHERE authid=$authid")
		or die("authid-query crapped itself");
	
	$row = mysql_fetch_array($result);
	$name = $row['full_name'];
	
	mysql_free_result($result);
	return $name;
}


// GET AUTHORS
function get_authors($bookid) {

  $result = db_query("SELECT full_name, pen_name, editor, attribution, authors.authid, url FROM authors, by_author WHERE by_author.bookid=$bookid and by_author.authid=authors.authid ORDER BY author_rank;")
     or die("get_authors just shit the bed");

     $x = 0;

     while ($row = mysql_fetch_array($result)) {
	     $authors[$x]["full_name"] = $row["full_name"];
	     $authors[$x]["pen_name"] = $row["pen_name"];
	     $authors[$x]["editor"] = $row["editor"];
	     $authors[$x]["authid"] = $row["authid"];
	     $authors[$x]["url"] = $row["url"];
	     $authors[$x]["attrib"] = $row["attribution"];
	     $x++;
     }
     
     return $authors;
     mysql_free_result ($result);
}


// GET BOOK FROM REVID
function get_book_from_revid($revid) {
    
	$bookid = "";
	
	$result = db_query("SELECT books.bookid FROM books, reviews WHERE reviews.bookid=books.bookid AND reviews.revid=$revid AND reviews.active=1")
	or die("get_get_from_revid has angst!");
			
	$row = mysql_fetch_array($result);
	$bookid = $row['bookid'];
			
	mysql_free_result($result);
	return($bookid);
}


// GET BYLINE
function get_byline($authors) {
	
  $byline = "";
	
  $by = $authors[0]["editor"] ? "edited by" : "by";
  $first_author = $authors[0]["full_name"];
  $first_pen_name = $authors[0]["pen_name"];
  $first_attrib = $authors[0]["attrib"];

  $byline = "$by ";
  
  if ($first_pen_name) {
	  
	  if ($first_attrib) {
		  $byline = $byline . $first_author . " writing as ";
	  }
	  $byline = $byline . $first_pen_name;
  }
  else {
	  $byline = $byline . $first_author;
  }
  
  $howmany = count($authors);
  
  if ($howmany == 2) {
	  $second_author = $authors[1]["full_name"];
	  $second_pen_name = $authors[1]["pen_name"];
	  $second_attrib = $authors[1]["attrib"];
	  
	  $byline = $byline . " and ";
	  
	  if ($second_pen_name) {
		  
		  if ($second_attrib) {
			  $byline = $byline . $second_author . " writing as ";
		  }
		  $byline = $byline . $second_pen_name;
	  }
	  else {
		  $byline = $byline . $second_author;
	  }
  }
    
  elseif ($howmany > 2) {
	  $x = 1;
	  $y = 1;
	  while ($x < ($howmany - 1)) {
		  $next_author = $authors[$y]["full_name"];
		  $next_pen_name = $authors[$y]["pen_name"];
		  $next_attrib = $authors[$y]["attrib"];
		  
		  $byline = $byline . ", ";
		  
		  if ($next_pen_name) {
			  
			  if ($next_attrib) {
				  $byline = $byline . $next_author . " writing as ";
			  }
			  $byline = $byline . $next_pen_name;
		  }
		  else {
			  $byline = $byline . $next_author;
		  }
		  
		  $x = $x + 1;
		  $y = $y + 1;
	  }
	  
	  // last author
	  $last_author = $authors[$y]["full_name"];
	  $last_pen_name = $authors[$y]["pen_name"];
	  $last_attrib = $authors[$y]["attrib"];
	  
	  $byline = $byline . ", and ";
	  
	  if ($last_pen_name) {
		  
		  if ($last_attrib) {
			  $byline = $byline . $last_author . " writing as ";
		  }
		  $byline = $byline . $last_pen_name;
	  }
	  else {
		  $byline = $byline . $last_author;
	  }
  }
  return $byline;
}


// GET FOCUS REVIEW
function get_focus_review() {

	$focus_review = "";
	
    $result = db_query("SELECT substring_index(content, '<p>', 2) as teaser, date_format(reviewed_date, '%M %e, %Y') as date, pub_date, reviews.revid, title, rating, reviewer, reviews.bookid FROM books, reviews WHERE reviews.bookid=books.bookid and reviews.active=1 ORDER BY reviewed_date desc, posted_date desc limit 1;")
      or die("display_focus_review just crapped its pants");

      $row = mysql_fetch_array($result);
      $teaser = $row["teaser"];
      $date = $row["date"];
      $pub = $row["pub_date"];
      $revid = $row["revid"];
      $title = $row["title"];
      $rating = $row["rating"];
      $reviewer = $row["reviewer"];
      $bookid = $row["bookid"];
      
      $authors = get_authors($bookid);
      $auth_str = get_byline($authors);
      $colored_rating = colorize($rating);
      $date_text = " (" . $pub . ")\n\n";
      $link = "?section=review&amp;sub=$revid";
      $reviewer_link = "<a href='?section=about&amp;sub=" . strtolower($reviewer) . "'>$reviewer</a>";
      
      $focus_review .= "<div id='focus'>";
      $focus_review .= "<h2>Latest Review</h2>\n\n";
      $focus_review .= "<p><span class='fancy'>$title</span> ";
      $focus_review .= $auth_str . "<br>\n";
      //$focus_review .= $date_text . "</h3>\n";
      $focus_review .= "Reviewed by $reviewer_link on $date<br>\n$colored_rating</p>\n\n$teaser<p class='small'><a href='$link'>Read full review >></a></p></div>\n\n";
    
      mysql_free_result($result);
      return $focus_review;
}


// GET GENRE
function get_genre($book) {
	
	global $genre_titles;
	
	$genre = "";
	
	$result = db_query("SELECT genre FROM books WHERE books.bookid=$book;")
	or die("get_genre has had a major malfunction");
	
	$row = mysql_fetch_array($result);
	$letter = $row['genre'];
	
	$genre = $genre_titles[$letter];
	
	return $genre;
	mysql_free_result($result);
}


// GET ISBN
function get_isbn($book) {
	
	$isbn = "";
	
	$result = db_query("SELECT isbn FROM books WHERE bookid=$book;")
	or die("get_isbn is cranky");
	
	$row = mysql_fetch_array($result);
	$isbn = $row['isbn'];
	
	return $isbn;
	mysql_free_result($result);
}


// GET LAST REVIEW
function get_last_review($who) {
	
  $last_review = "";
  
  $result = db_query("SELECT reviews.revid, reviews.bookid, title, date_format(reviewed_date, '%M %e, %Y') as date FROM books, reviews WHERE reviews.reviewer='$who' AND reviews.bookid=books.bookid AND reviews.active=1 ORDER BY reviewed_date desc, posted_date desc limit 1;")
    or die("Error: bad query");
  
  	$row = mysql_fetch_array($result);
  	$revid = $row["revid"];
  	$bookid = $row["bookid"];
  	$title = $row["title"];
    $date = $row["date"];
    
    $authors = get_authors($bookid);
	$auth_str = get_byline($authors);
    
    $last_review .= "<p><a href='?section=review&amp;sub=$revid'>$title</a><br>\n";
    $last_review .= "$auth_str<br>$date</p>\n\n";
    
    mysql_free_result($result);
    return $last_review;
  
}


// GET LATEST REVIEWS
function get_latest_reviews() {
	
    $result = db_query("SELECT reviews.revid, title, reviewer, rating, reviews.bookid FROM books, reviews WHERE reviews.bookid=books.bookid and reviews.active=1 ORDER BY reviewed_date desc, posted_date desc limit 1,6;")
    or die("display_latest_reviews had a nervous breakdown");
      
    $latest_reviews = "<h2>More Reviews</h2>\n";
    
    while ($row = mysql_fetch_array($result)) {
	    $revid = $row["revid"];
	    $title = $row["title"];
	    $reviewer = $row["reviewer"];
	    $rating = $row["rating"];
	    $bookid = $row["bookid"];
	
	    $authors = get_authors($bookid);
		$auth_str = get_byline($authors);
		
		$colored_rating = colorize($rating);
		
		$latest_reviews .= "<p>";
		$latest_reviews .= "<em><a href='?section=review&amp;sub=$revid'>$title</a></em><br>";
		$latest_reviews .= $auth_str;
		$latest_reviews .= "\n\n<br>Reviewed by $reviewer</p>\n\n";
	}
	mysql_free_result ($result);
    return $latest_reviews;
}


// GET MORE REVIEWS BY REVID
function get_more_reviews_by_revid($revid) {
	
	global $rating_text;
	
	$more_reviews = "";
	$bookid = get_book_from_revid($revid);
	$reviews = get_reviews($bookid);
	
	if (count($reviews) > 1) {
		for ($y=0; $y<count($reviews); $y++) {
			$newrevid = $reviews[$y]["revid"];
			$rev_name = $reviews[$y]["reviewer"];
			//$rating = $rating_text[$reviews[$y]["raw_rating"]];
			if ($newrevid != $revid) {
				$link = "<li><a href='?section=review&amp;sub=$newrevid'>$rev_name's Review<br></a></li>\n";
				$more_reviews .= $link;
			}
		}
	}
	return($more_reviews);
}


// GET NEXT REVIEW ID
function get_next($rev) {
	
    $maxresult = db_query("SELECT MAX(revid) as maxid FROM reviews WHERE reviews.active=1;")
      or die("get_next maxresult just puked up a lung");
      
      $maxrow = mysql_fetch_array($maxresult);
      $maxid = $maxrow['maxid'];

      if ($rev == $maxid) {
	      return 0;
      }
      else {
	      $qid = 0;
	      
	      while ($qid == 0) {
		      $rev = $rev + 1;
		      
		      $result = db_query("SELECT revid FROM reviews WHERE reviews.revid=$rev and reviews.active=1;")
		        or die("get_next main query has had a meltdown");
		        
		        $row = mysql_fetch_array($result);
		        $qid = $row['revid'];
		        
		        if (($qid == 0) && ($rev == $maxid)) {
			        break;
		        }
	        }
	        return $qid;
	        mysql_free_result($maxresult);
	        mysql_free_result($result);
        }
}


// GET PREVIOUS REVIEW ID
function get_prev($rev) {
	
	if ($rev == 1) {
		return 0;
	}
	else {
		$qid = 0;
		
		while ($qid == 0) {
			$rev = $rev - 1;
			
			$result = db_query("SELECT revid FROM reviews WHERE reviews.revid=$rev and reviews.active=1;")
			  or die("get_prev is having emotional difficulties");
			  
			  $row = mysql_fetch_array($result);
			  $qid = $row['revid'];
			  
			  if (($qid == 0) && ($rev == 1)) {
				  break;
			  }
		  }
		  return $qid;
		  mysql_free_result($result);
	  }
}


// GET PUB DATE
function get_pub_date($book) {
	
	$pub_date = "";
	
	$result = db_query("SELECT pub_date FROM books WHERE bookid=$book;")
	or die("get_pub_date is having a bad hair day");
	
	$row = mysql_fetch_array($result);
	$pub_date = $row['pub_date'];
	
	return $pub_date;
	mysql_free_result($result);
}


// GET REVIEW PAGE TITLE
function get_review_page_title($revid) {
	
	$page_title = "";
	
	$result = db_query("SELECT title, books.bookid FROM books, reviews WHERE reviews.bookid=books.bookid AND reviews.revid=$revid AND reviews.active=1;")
	or die("get_page_title has angst!");
			
	$row = mysql_fetch_array($result);
	$book = $row['bookid'];
	$booktitle = $row['title'];
			
	// Review is valid
	if ($book) {
		$who = get_byline(get_authors($book));
		$page_title = $booktitle . " " . $who;
	}
	mysql_free_result($result);
	return($page_title);
}


// GET REVIEWER
function get_reviewer($revid) {
	
	$reviewer = "";
	
	$result = db_query("SELECT reviewer FROM reviews WHERE reviews.revid=$revid")
	or die("get_get_from_revid has angst!");
    
	$row = mysql_fetch_array($result);
	$reviewer = $row['reviewer'];
	
	mysql_free_result($result);
	return($reviewer);
	
}


// GET REVIEWER STATISTICS
function get_reviewer_statistics($who) {
	
	$reviewer_stats = "";
	
	// Fantasy Review Count
	$fantasy_result = db_query("SELECT COUNT(reviews.revid) AS fcount FROM reviews, books WHERE reviewer='$who' AND reviews.bookid=books.bookid AND books.genre='F' AND active=1;")
	or die("get_reviewer_statistics takes double damage from Fantasy");
	$row = mysql_fetch_array($fantasy_result);
	$fcount = $row["fcount"];
	mysql_free_result($fantasy_result);
	
	// Sci-Fi Review Count
	$scifi_result = db_query("SELECT COUNT(reviews.revid) AS sfcount FROM reviews, books WHERE reviewer='$who' AND reviews.bookid=books.bookid AND books.genre='SF' AND active=1;")
	or die("get_reviewer_statistics takes half damage from Sci-Fi");
	$row = mysql_fetch_array($scifi_result);
	$sfcount = $row["sfcount"];
	mysql_free_result($scifi_result);
	
	// Total Reviews
	$tcount = $fcount + $sfcount;
	
	$reviewer_stats .= "<p style=letter-spacing:1px>\n";
	$reviewer_stats .= "Fantasy Reviews: $fcount<br>\n";
	$reviewer_stats .= "Sci-Fi Reviews: $sfcount<br>\n";
	$reviewer_stats .= "Total: $tcount\n";
	$reviewer_stats .= "</p>\n\n";
	
	return $reviewer_stats;
}
  

// GET REVIEWS
function get_reviews($bookid) {
	
    $result = db_query("SELECT revid, reviewer, rating, date_format(reviewed_date, '%M %e, %Y') as date FROM reviews WHERE reviews.bookid=$bookid and reviews.active=1 ORDER BY reviewed_date;")
     or die("get_reviews just screwed the pooch");
     
    $x = 0;
     
    while ($row = mysql_fetch_array($result)) {
	     $reviews[$x]["revid"] = $row["revid"];
	     $reviews[$x]["reviewer"] = $row["reviewer"];
	     $reviews[$x]["reviewed_date"] = $row["date"];
	     
	     $rating = $row["rating"];
	     $colored_rating = colorize($rating);
         
         $reviews[$x]["raw_rating"] = $rating;
	     $reviews[$x]["rating"] = $colored_rating;
	     $x++;
    }
     
    return $reviews;
    mysql_free_result ($result);
}


// GET REVIEWS BY REVIEWER INDEX
function get_reviews_by_reviewer_index() {
	
	$reviewer_index = "";
	
	global $reviewer_names;
	
	$reviewer_index .= "<h2>Book Reviews:<br>by Reviewer</h2>\n\n";
	$reviewer_index .= "<ul>\n";
	foreach ($reviewer_names as $name => $value) {
		$reviewer_index .= "<li><a href='?section=reviewer&amp;sub=$name'>$value" . "'s" . " Reviews</a></li>\n";
	}
	$reviewer_index .= "</ul>\n\n";
	return $reviewer_index;
}


// GET SINGLE AUTHOR REVIEWS
function get_single_author_reviews($who) {
	
	global $nums;

	$review_list = "";
	
    $authresult = db_query("SELECT full_name FROM authors WHERE authid=$who;")
      or die("display_single_author authresult demands a recount");
      
    $arow = mysql_fetch_array($authresult);
    $authname = $arow['full_name'];

    $result = db_query("SELECT authreviews.bookid, authreviews.revid, authreviews.reviewer, authreviews.rating, date_format(authreviews.reviewed_date, '%M %e, %Y') as date, title, series_name, series_order, pub_date FROM books INNER JOIN (SELECT reviews.bookid, revid, reviewer, reviewed_date, rating FROM reviews INNER JOIN (SELECT bookid FROM by_author WHERE authid=$who) as booklist ON reviews.bookid=booklist.bookid WHERE active=1) as authreviews ON books.bookid=authreviews.bookid ORDER BY series_name desc, series_order, pub_date desc, rating desc, reviewed_date desc;")
    or die("display_single_author wants its money back");
    
    $num_reviews = mysql_num_rows($result);
    
    $current_series = "";
    $new_series = "";
    
    $current_book = "";
    $new_book = "";
    
    $current_num = "";
    $new_num = "";
    
    $first_single = 1;
    $prefix = 0;
    
    $review_list .= "<h2>$authname</h2>\n";
    
    // Make sure there are reviews to display.
    if ($num_reviews) {
	    
	    for ($x=0; $x<$num_reviews; $x++) {
		    
		    $row = mysql_fetch_array($result);
		    $series = $row["series_name"];
		    $new_book = $row["title"];
		    
		    // SERIES
		    if ($series) {
			    
			    $new_series = $series;
			    $new_num = $row["series_order"];
			    $prefix = 1;
			    
			    if ($new_series<>$current_series) {
				  
				    if ($current_series != "") { $review_list .= "\n</ul>\n</ul>\n"; }
				      
				    $review_list .= "<h3>Series: $new_series</h3>\n<ul style='padding-bottom:20px'>\n";
				    $current_series = $new_series;
				    $no_series_change = 0;
			    }
			    else { //series didn't change
			        $no_series_change = 1;
		        }
		    }
		    else {
			    $prefix = 0;
		    }
		    // CHECK IF FIRST STANDALONE
		    if ($first_single && $prefix==0) {
			    
			    if ($current_series != "") { $review_list .= "\n</ul>\n</ul>\n"; }
			    
			    $review_list .= "<h3>Standalone Novels &amp; Collections</h3>\n<ul style='padding-bottom:20px'>\n";
			    $first_single = 0;
			    $no_series_change = 0;
		    }
		    elseif ($prefix == 0) {
			    $no_series_change = 1;
		    }    
		    // BOOK
		    if ($new_book<>$current_book) {

			    if ($no_series_change) {
				    $review_list .= "\n</ul>\n";
				}			                    
			    
			    
			    $review_list .= "\n<li style='list-style:none'>\n";
			    
			    if ($prefix) {
				    
				    $num_word = $nums[$new_num];
				    $text = "<strong>Book " . $num_word . ":</strong> ";
				    $review_list .= $text;
				    $current_num = $new_num;
			    }
			    
			    $book_text = "<strong><em>" . $new_book . "</em> ";
			    $review_list .= $book_text;
			    $current_book = $new_book;
			    
			    $date = $row["pub_date"];
			    if ($date) {
				    $date_text = "(" . $date . ") ";
				    $review_list .= $date_text;
			    }
			    
			    $bookid = $row["bookid"];
			    $authors = get_authors($bookid);
			    $auth_str = get_byline($authors);
			    $review_list .= $auth_str . "</strong>";
			    
			    $review_list .= "\n<ul style='padding-bottom:20px'>\n";
		    }
		    // REVIEW
		    $rating = $row["rating"];
   		    $revid = $row["revid"];
   		    $when = $row["date"];

		    $colored_rating = colorize($rating);
			$link = "<a href='?section=review&amp;sub=" . $revid . "'>";			
   		    $rev_text = $link . "Reviewed by " . $row["reviewer"] . "</a> ";
   		    
		    $review_list .= "<li style='list-style:none'>";
		    $review_list .= $rev_text;
			//$review_list .= $when . ": ";
		    $review_list .= $colored_rating;
		    $review_list .= "</li>\n";
		   
		    }
		$review_list .= "\n</ul>\n</ul>\n";
		
        mysql_free_result($authresult);
	    mysql_free_result($result);
	    return $review_list;
	}
	else { // Result set was empty.
		mysql_free_result($authresult);
		mysql_free_result($result);
		return "";
	}
}


// GET SINGLE REVIEW
function get_single_review($revid) {
		
		global $nums;
	  
	  	$review_text = "";
	  
	  	$result = db_query("SELECT date_format(reviewed_date, '%M %e, %Y') as date, pub_date, title, reviewer, rating, content, copyright, series_name, series_order, isbn, brief, books.bookid, awards FROM books, reviews WHERE reviews.revid=$revid and reviews.bookid=books.bookid and active=1;")
	  	or die("get_single_review just wet itself");
	       
		$row = mysql_fetch_array($result);
		mysql_free_result ($result);
		            
		if (!$row) {
			return $review_text;
	    }
	    else {
		    
		    $date = $row["date"];
		    $pub = $row["pub_date"];
		    $title = $row["title"];
		    $reviewer = $row["reviewer"];
		    $rating = $row["rating"];
            $content = $row["content"];
            $copyright = $row["copyright"];
            $series = $row["series_name"];
            $series_order = $row["series_order"];
            $seriesnum = $nums[$series_order];
            $isbn = $row["isbn"];
            $brief = $row["brief"];
            $bookid = $row["bookid"];
            $awards = $row["awards"];
			           
			$authors = get_authors($bookid);
			$colored_rating = colorize($rating);
			           
			$prev = get_prev($revid);
			$next = get_next($revid);
			              
			$prevstr = ($prev) ? "<a href='?section=review&amp;sub=$prev'>prev</a>" : "prev";
			$nextstr = ($next) ? "<a href='?section=review&amp;sub=$next'>next</a>" : "next";
			           
			$auth_str = "<p class='byline'>" . get_byline($authors) . "</p>\n";
			$date_text = $pub;
			           
			$reviewer_link = "<a href='?section=about&amp;sub=" . strtolower($reviewer) . "'>$reviewer</a>";
			           
			$review_text .= "<h2 class='cinch'>$title</h2>\n";
			$review_text .= $auth_str;
			$review_text .= "<p>";
			if ($series) $review_text .= "<span class='series'>$series: Book $seriesnum</span><br>\n";
			           
			$review_text .= "$brief";
			           
			if ($awards != "") $review_text .= " $awards\n\n";
			           
			$review_text .= "</p>\n";
			           
			$review_text .= "<p><span class='byline'>Reviewed by $reviewer_link</span>.  $colored_rating.<br>\n";
			$review_text .= "<span class='small'>$date | Revid $revid < $prevstr | $nextstr ></span></p>\n\n";
			           
			$review_text .= "$content\n\n";
			if ($copyright) {
				$review_text .= file_get_contents($copyright) . "\n";
		    }
			else {
				$review_text .= "<p class='copy'>&copy; All Rights Reserved</p>\n";
		    }
			$review_text .= "<p class='qnav'>< $prevstr | <a href='?section=review&amp;sub=$revid#top'>top</a> | $nextstr ></p>\n\n";
		}
		return $review_text;
}
       

// GET SINGLE REVIEWER REVIEWS
function get_single_reviewer_reviews($who) {
	// $who = reviewer string
	// NOTE: do NOT establish urls with 'formatted' reviewer names;
	// i.e., keep them lower case.

	global $reviewer_names;
	global $genre_titles;
	
	$output = "";
	
	$which = $reviewer_names[$who];
	
	if ($which) {
		
        $result = db_query("SELECT reviews.revid, reviews.rating, tribble.title, tribble.genre, tribble.bookid, tribble.last_name FROM reviews, (SELECT books.title, books.genre, books.bookid, ifnull(by_author.pen_last_name, authors.last_name) as last_name FROM books, by_author, authors WHERE by_author.author_rank=1 and books.bookid=by_author.bookid and by_author.authid=authors.authid) as tribble WHERE reviews.reviewer='$which' and reviews.bookid=tribble.bookid and reviews.active=1 ORDER BY tribble.genre, reviews.rating desc, tribble.last_name;")
        or die("display_single_reviewer just had a conniption");
      
        $num_reviews = mysql_num_rows($result);
      
        $current_genre = "";
        $new_genre = "";
      
        $current_rating = 0;
        $new_rating = 0;
        
        $output .= "<h2>$which" . "'s " . "Reviews</h2>\n";
      
        for ($x=0; $x<$num_reviews; $x++) {
	      
	        $row = mysql_fetch_array($result);
	        $raw_genre = $row["genre"];
	        $rating = $row["rating"];
	        $revid = $row["revid"];
	        $bookid = $row["bookid"];
	        $title = $row["title"];
	      
	        $genre = $genre_titles[$raw_genre];
	        $new_genre = $genre;
	        $new_rating = $rating;
	      
	        if ($new_genre<>$current_genre) {
		        
		        if ($current_genre != "") { $output .= "\n</ul>\n</ul>\n"; }
		        
		        $output .= "<h3><em>$new_genre</em></h3>\n<ul style='padding-bottom:20px'>\n\n\n";
		        
		        $no_genre_change = 0;
		        $current_rating = "";
		        
	        }
	        else { //genre didn't change
			        $no_genre_change = 1;
		        }
		        
	        if ($new_rating<>$current_rating) {
		        
		          if ($no_genre_change) {
				    $output .= "\n</ul>\n";
				}
				
		        $colored_rating = colorize($rating);
		        $output .= "\n<li style='list-style:none'>\n<h4>$colored_rating</h4>\n\n<ul style='padding-bottom:20px'>\n";
	        }
	        $authors = get_authors($bookid);
	        $output .= "<li style='list-style:none'><a href='?section=review&amp;sub=$revid'>";
	        $output .= $title;
	        $output .= "</a> ";
	        $auth_str = get_byline($authors);
	        $output .= $auth_str;
	        $output .= "\n</li>\n";
	        $current_genre = $new_genre;
	        $current_rating = $new_rating;
        }
        $output .= "\n</ul>\n</ul>\n";
        mysql_free_result($result);
    }
    return("$output");
}
  
  
// GET WEBSITE STATISTICS
function get_website_statistics() {
	
	$statistics = "";
	
	// Fantasy Review Count
	$fantasy_result = db_query("SELECT COUNT(reviews.revid) AS fcount FROM reviews, books WHERE reviews.active=1 AND reviews.bookid=books.bookid AND books.genre='F'")
	or die("get_website_statistics takes 4 points of damage from Fantasy");	
	$row = mysql_fetch_array($fantasy_result);
	$fcount = $row["fcount"];
	mysql_free_result($fantasy_result);
	
	// Sci-Fi Review Count
	$scifi_result = db_query("SELECT COUNT(reviews.revid) AS sfcount FROM reviews, books WHERE reviews.active=1 AND reviews.bookid=books.bookid AND books.genre='SF'")
	or die("get_website_statistics takes 6 points of damage from Sci-Fi");
	$row = mysql_fetch_array($scifi_result);
	$sfcount = $row["sfcount"];
	mysql_free_result($scifi_result);
	
	// Authors Count
	$auth_result = db_query("SELECT COUNT(authors.authid) AS acount FROM authors WHERE authors.full_name IS NOT NULL")
	or die("get_website_statistics takes 8 points of damage from various authors");
	$row = mysql_fetch_array($auth_result);
	$acount = $row["acount"];
	mysql_free_result($auth_result);
	
	// Books Count
	$book_result = db_query("SELECT COUNT(books.bookid) AS bcount FROM books WHERE books.title IS NOT NULL")
	or die("get_website_statistics takes 12 points of damage from books");
	$row = mysql_fetch_array($book_result);
	$bcount = $row["bcount"];
	mysql_free_result($book_result);
	
	$tcount = $fcount + $sfcount;
	
	$statistics .= "<h2>Live Statistics</h2>\n\n";
	
	$statistics .= "<p style=letter-spacing:1px>\n";
	$statistics .= "Fantasy Reviews: $fcount<br>\n";
	$statistics .= "Sci-Fi Reviews: $sfcount<br>\n";
	$statistics .= "Total: $tcount\n";
	$statistics .= "</p>\n\n";
	
	$statistics .= "<p style=letter-spacing:1px>\n";
	$statistics .= "Authors Reviewed: $acount<br>\n";
	$statistics .= "Books Reviewed: $bcount<br>\n";
	$statistics .= "</p>\n\n";
	
	return $statistics;
}


// SEARCH FOR
function search_for($x, $raw_sub) {
	
	global $nums;
	
	$matches = 0;
	$genre = "";
	
	$search_result = "";
	
	// Take out escape characters for logging and echoing to user
	$stripped = stripslashes($raw_sub);
	
	// Logging
	$when = date("[m-d-Y | h:ia]");
	$ip = $_SERVER["REMOTE_ADDR"];
	$ip_form = "[" . str_pad($ip, 15) . "]";
	$what = $when . " " . $ip_form . " " . $stripped . "\n";
    $log = fopen("search_query_log.txt", "a");
    fwrite($log, $what);
    fclose($log);
	
	// Searching for nothing!
	if ($x == "") {
		$search_result .= "<p><em>Searching for nothing will get you nowhere.</em></p>\n\n";
		return($search_result);
	}
	
	// Genre limiter for Fantasy
	if ((stristr($x, "fantasy")) || (stristr($x, "-f"))) {
		$genre = "F";
	}
	
	// Genre limiter for SF, sci-fi, scifi, science-fiction
	$xpad = " " . $x . " ";
	$y = str_replace("-", " ", $xpad);
	
	if ((stristr($y, " sf ")) || (stristr($y, " sci fi ")) || (stristr($y, " scifi ")) || (stristr($y, " science fiction ")) || (stristr($y, " -sf "))) {
		$genre = "SF";
	}
	
	// Finder's strip search
	$badchars = array("!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "{", "}", "[", "]", "|", ":", ";", "<", ">", "?", "/", "~", "`");
	$fx = str_ireplace($badchars, " ", $x);

    // Check for matching authors and pen-names
    $result = db_query("(SELECT authid, pen_name as full_name, pen_last_name as last_name FROM by_author WHERE pen_name<>'' and MATCH (pen_name) AGAINST (\"$fx\" IN BOOLEAN MODE)) UNION (SELECT authid, full_name, last_name FROM authors WHERE full_name<>'' and MATCH (full_name) AGAINST (\"$fx\" IN BOOLEAN MODE)) ORDER BY last_name asc")
    or die("search_for author-query = FAIL");
    
    $search_result .= "<h2>Search Results for <em>$stripped</em></h2>\n\n";
    //$search_result .= "<h3>Search terms: <em>$stripped</em></h3>\n\n";
    
    //$search_result .= "<p><strong>UNstripped search terms:</strong> $x</p>\n\n";

    if (mysql_num_rows($result)) {
	    $matches = 1;
	    $search_result .= "<h3>Matching Author Names</h3>\n\n";
	    $search_result .= "<ul style='list-style:none'>\n";
	    while ($row = mysql_fetch_array($result)) {
		    $full_name = $row["full_name"];
		    $authid = $row["authid"];
     	    $url = "?section=author&amp;sub=$authid";
     	    // Display link to author page
		    $search_result .= "<li style='padding-bottom:20px'><a href='$url'>$full_name</a></li>\n";
        }
        $search_result .= "</ul>\n\n";
    }
        
    // Check for matching titles, series
    // Genre limiter determines select query
    if ($genre) {
	    $squery = "SELECT * FROM books WHERE genre='$genre' AND MATCH (title, series_name) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name desc, series_order, pub_date desc";
    }
    else {
	    $squery = "SELECT * FROM books WHERE MATCH (title, series_name) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name desc, series_order, pub_date desc";
    }
    
    $result = db_query($squery)
    or die("search_for title-query = FAIL");
    
    if (mysql_num_rows($result)) {
	    $matches = 1;
	    $search_result .= "<h3>Matching Book Series and Titles</h3>\n\n";
	    $search_result .= "<ul style='padding-bottom:20px'>\n";
	    while ($row = mysql_fetch_array($result)) {
		    $series = $row["series_name"];
		    $series_order = $row["series_order"];
		    $seriesnum = $nums[$series_order];
		    $title = "<em>" . $row["title"] . "</em>";
		    $pubdate = $row["pub_date"];
		    $bookid = $row["bookid"];
		    $who = get_byline(get_authors($bookid));
		    $search_result .= "<li style='list-style: none'>";
		    // Display series, title, author, pub date
		    if ($series) $search_result .= "<strong>$series, Book $seriesnum:</strong> ";
   		    $search_result .= "<strong>$title</strong> $who ($pubdate)\n";
   		    // Display list of available reviews
   		    $reviews = get_reviews($bookid);
   		    $search_result .= "<ul style='padding-bottom:20px'>\n";
   		    if ($reviews) {
	   		    for ($y=0; $y<count($reviews); $y++) {
			       $newrevid = $reviews[$y]["revid"];
			       $rev_name = $reviews[$y]["reviewer"];
			       $reviewed_date = $reviews[$y]["reviewed_date"];
			       $rating = $reviews[$y]["raw_rating"];
			       $colored_rating = colorize($rating);
			       $link = "<li style='list-style: none'><a href='?section=review&amp;sub=$newrevid'>Reviewed by $rev_name</a> $colored_rating</li>\n";
			       $search_result .= $link;
			       }
	        }
		    $search_result .= "</ul>\n\n";
        }
        $search_result .= "</ul>\n\n";
    }
        
    // Check for matching ISBN
    // Strip dashes from potential ISBN query
    $nodash = str_replace("-", "", $fx);
    
    // Strip dashes from ISBN field during match
    $result = db_query("SELECT * FROM books WHERE REPLACE(books.isbn, '-', '')='$nodash'")
      or die("search_for isbn-query has indigestion");
      
      if (mysql_num_rows($result)) {
	      $matches = 1;
	      $search_result .= "<h3>Matching ISBNs</h3>\n\n";
	      while ($row = mysql_fetch_array($result)) {
		      $series = $row["series_name"];
		      $series_order = $row["series_order"];
		      $seriesnum = $nums[$series_order];
		      $title = "<em>" . $row["title"] . "</em>";
		      $pubdate = $row["pub_date"];
		      $bookid = $row["bookid"];
		      $who = get_byline(get_authors($bookid));
		      $search_result .= "<ul style='padding-bottom:20px'>\n<li style='list-style: none'>\n";
		      // Display series, title, author, pub date
		      if ($series) $search_result .= "<strong>$series, Book $seriesnum:</strong> ";
   		      $search_result .= "<strong>$title</strong> $who ($pubdate)\n";
   		      // Display list of available reviews
   		      $reviews = get_reviews($bookid);
   		      $search_result .= "<ul style='padding-bottom:20px'>\n";
   		      if ($reviews) {
	   		      for ($y=0; $y<count($reviews); $y++) {
		   		      $newrevid = $reviews[$y]["revid"];
			          $rev_name = $reviews[$y]["reviewer"];
			          $reviewed_date = $reviews[$y]["reviewed_date"];
			          $rating = $reviews[$y]["raw_rating"];
			          $colored_rating = colorize($rating);
			          $link = "<li style='list-style: none'><a href='?section=review&amp;sub=$newrevid'>Reviewed by $rev_name</a> $colored_rating</li>\n";
			          $search_result .= $link;
		          }
		          $search_result .= "</ul>\n";
	          }
	          $search_result .= "</li>\n</ul>\n\n";
          }
      }
   
    // Check for matching briefs
    // Genre limiter determines select query
    if ($genre) {
	    $squery = "SELECT * FROM books WHERE genre='$genre' AND MATCH (brief, awards) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name, series_order asc";
    }
    else {
	    $squery = "SELECT * FROM books WHERE MATCH (brief, awards) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name, series_order asc";
    }
    
    $result = db_query($squery)
    or die("search_for brief-query = FAIL");
    
    if (mysql_num_rows($result)) {
	    $matches = 1;
	    $search_result .= "<h3>Matching Book Descriptions</h3>\n\n";
	    $search_result .= "<ul style='padding-bottom:20px'>\n";
	    while ($row = mysql_fetch_array($result)) {
		    $series = $row["series_name"];
		    $series_order = $row["series_order"];
		    $seriesnum = $nums[$series_order];
		    $title = "<em>" . $row["title"] . "</em>";
		    $brief = $row["brief"] . " " . $row["awards"];
		    $pubdate = $row["pub_date"];
		    $bookid = $row["bookid"];
		    $who = get_byline(get_authors($bookid));
		    $search_result .= "<li style='list-style: none'>";
		    // Display series, title, author, pub date, brief
		    if ($series) $search_result .= "<strong>$series, Book $seriesnum:</strong> ";
   		    $search_result .= "<strong>$title</strong> $who ($pubdate)<br>\n";
		    $search_result .= $brief . "\n";
		    // Display list of available reviews
		    $reviews = get_reviews($bookid);
   		    $search_result .= "<ul style='padding-bottom:20px'>\n";
   		    if ($reviews) {
	   		    for ($y=0; $y<count($reviews); $y++) {
			       $newrevid = $reviews[$y]["revid"];
			       $rev_name = $reviews[$y]["reviewer"];
			       $reviewed_date = $reviews[$y]["reviewed_date"];
			       $rating = $reviews[$y]["raw_rating"];
			       $colored_rating = colorize($rating);
			       $link = "<li style='list-style: none'><a href='?section=review&amp;sub=$newrevid'>Reviewed by $rev_name</a> $colored_rating</li>\n";
			       $search_result .= $link;
			       }
	        }
		    $search_result .= "</ul>\n\n";
	    }
	    $search_result .= "</ul>\n\n";
    }
    
    // Check for matching review content
    // Genre limiter determines select query
    if ($genre) {
	    $squery = "SELECT revid, reviewer, rating, reviews.bookid, date_format(reviewed_date, '%M %e, %Y') as date FROM reviews, books WHERE reviews.bookid=books.bookid AND books.genre='$genre' AND reviews.active=1 AND MATCH (content) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY reviewed_date asc";
    }
    else {
	    $squery = "SELECT revid, reviewer, rating, bookid, date_format(reviewed_date, '%M %e, %Y') as date FROM reviews WHERE active=1 and MATCH (content) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY reviewed_date asc";
    }
    
    $result = db_query($squery)
    or die("search_for content-query = FAIL");
    
    if (mysql_num_rows($result)) {
	    $matches = 1;
	    $search_result .= "<h3>Matching Reviews</h3>\n\n";
	    $search_result .= "<p><em>The following reviews contain your search terms:</em></p>\n\n";
   	    $search_result .= "<ul style='padding-bottom:20px'>\n";
	    while ($row = mysql_fetch_array($result)) {
		    $revid = $row["revid"];
		    $rev_name = $row["reviewer"];
		    $rev_date = $row["date"];
		    $rating = $row["rating"];
		    $colored_rating = colorize($rating);
		    $bookid = $row["bookid"];
		    $revresult = db_query("SELECT * FROM books WHERE bookid=$bookid")
		      or die("search_for book-query = FAIL");
		    $revrow = mysql_fetch_array($revresult);
			$series = $revrow["series_name"];
		    $series_order = $revrow["series_order"];
		    $seriesnum = $nums[$series_order];
		    $title = "<em>" . $revrow["title"] . "</em>";
		    $pubdate = $revrow["pub_date"];
		    $bookid = $revrow["bookid"];
		    $who = get_byline(get_authors($bookid));
   		    $link = "<a href='?section=review&amp;sub=$revid'>Reviewed by $rev_name</a> $colored_rating\n";
		    // Display series, title, author, pub date
		    $search_result .= "<li style='list-style: none;padding-bottom:20px'>";
		    if ($series) $search_result .= "<strong>$series, Book $seriesnum:</strong> ";
		    $search_result .= "<strong>$title</strong> $who ($pubdate)<br>\n";
			$search_result .= $link;
		    $search_result .= "</li>\n\n";
	    }
	    $search_result .= "</ul>\n\n";
	    mysql_free_result($revresult);
    }
      
    // Check for matching publication date
    // Genre limiter determines select query
    if ($genre) {
	    $squery = "SELECT * FROM books WHERE genre='$genre' AND MATCH (pub_date) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name, series_order asc";
    }
    else {
	    $squery = "SELECT * FROM books WHERE MATCH (pub_date) AGAINST (\"$fx\" IN BOOLEAN MODE) ORDER BY series_name, series_order asc";
    }
    
    $result = db_query($squery)
    or die("search_for date-query = FAIL");
    
    if (mysql_num_rows($result)) {
	    $matches = 1;
	    $search_result .= "<h3>Matching Book Publication Date</h3>\n\n";
	    $search_result .= "<ul style='padding-bottom:20px'>\n";
	    while ($row = mysql_fetch_array($result)) {
		    $series = $row["series_name"];
		    $series_order = $row["series_order"];
		    $seriesnum = $nums[$series_order];
		    $title = "<em>" . $row["title"] . "</em>";
		    $pubdate = $row["pub_date"];
		    $bookid = $row["bookid"];
		    $who = get_byline(get_authors($bookid));
		    $search_result .= "<li style='list-style: none'>";
		    // Display series, title, author, pub date
		    if ($series) $search_result .= "<strong>$series, Book $seriesnum:</strong> ";
   		    $search_result .= "<strong>$title</strong> $who ($pubdate)\n";
		    // Display list of available reviews
		    $reviews = get_reviews($bookid);
   		    $search_result .= "<ul style='padding-bottom:20px'>\n";
   		    if ($reviews) {
	   		    for ($y=0; $y<count($reviews); $y++) {
			       $newrevid = $reviews[$y]["revid"];
			       $rev_name = $reviews[$y]["reviewer"];
			       $reviewed_date = $reviews[$y]["reviewed_date"];
			       $rating = $reviews[$y]["raw_rating"];
			       $colored_rating = colorize($rating);
			       $link = "<li style='list-style: none'><a href='?section=review&amp;sub=$newrevid'>Reviewed by $rev_name</a> $colored_rating</li>\n";
			       $search_result .= $link;
			       }
	        }
		    $search_result .= "</ul>\n\n";
	    }
	    $search_result .= "</ul>\n\n";
    }
    // No match on any field
    if ($matches == 0) $search_result .= "Sorry, couldn't find your search terms anywhere.";
    mysql_free_result($result);
    return($search_result);
}

?>
