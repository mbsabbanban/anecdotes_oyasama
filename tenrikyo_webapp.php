<?php
  // 1. Create a database connection
  $dbhost = "localhost";
  $dbuser = "mattsabb_tenri";
  $dbpass = "oyasama";
  $dbname = "mattsabb_tenrikyo_webapp";
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  
  //Test if connection occurred/
  //if(mysqli_connect_errno()){
    //die("Database connection failed: " .
		//mysqli_connect_error() .
		//" (" . mysqli_connect_errno() . ")"
		//);
  //} else { 
  //print "Connection to the database is established\n";
  //echo "<br>";
  //echo "<br>";
  //}
?>

<!DOCTYPE HTML>
  <head>
	<meta charset="utf-8">
	<!--Version Number 1.2-->
	<title>Tenrikyo: Anecdotes of Oyasama</title>
	<meta name="description" content="Welcome to the homepage of the Tenrikyo Oyasama Anecdotes webapp">
	<link rel="stylesheet" type="text/css" href="tenrikyo_style.css"/>
	<!--Linking to the CSS -->
  </head>

 <body>
	<header>
			<div class="bg-header">
				<img id="header_logo" src="emblem_100x100.png" alt="tenrikyo_logo">
				<h1>Tenrikyo: Anecdotes of Oyasama</h1>
			</div>
	</header>
	<div id="wrapper">

		<div id="core" class="clearfix">
			<section id="left">
				<p>Find Anecdotes</p>
				<div id="form" class="formfix">
				<!-- Form validation takes place here because of HTML5 -->
					<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" name="searchForm">
					<input type="number" placeholder="Story Number" name="Story_Number" pattern="[-0-9]+"></br>
					<input type="text" placeholder="Title" name="Title"></br>
					<input type="text" placeholder="Content like..." name="Content"></br>
					<input type="number" placeholder="Page number" name="Page_Number" pattern="[-0-9]+"></br>
					<input type="submit" class="submit" name="search" value="Search">
					<input type="submit" class="submit" name="viewAll" value="View All">
					</form>
				<!-- I'm going to create another button called "View All" -->
					<?php
					  // Going to Declare the queryStop variable to validate that form contains data
					  $queryStop = false;
					  //if (isset($_POST['Search'])){
					  // The following prints all of the defined PHP variables
					  // print_r(get_defined_vars());
					  // REQUEST_METHOD checks the form to make sure that this is a POST request
					  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['search'])){
					  if(strlen($_POST['Title'])<=0 && strlen($_POST['Content'])<=0 && strlen($_POST['Story_Number'])<=0 && strlen($_POST['Page_Number'])<=0) {
					  echo "<br/>";
					  echo "<p><font size=" . '3" color="red">Please insert search terms!</font></p>';
					  $queryStop = true;} else {
					  echo "<br/>";
					  echo "Search for : ";
					  if ($_POST['Story_Number'] != '') {echo htmlspecialchars($_POST['Story_Number']) . "<br>";}
					  if ($_POST['Title'] != '') {echo htmlspecialchars($_POST['Title']) . "<br>";}
					  if ($_POST['Content'] != '') {echo htmlspecialchars($_POST['Content']) . "<br>";}
					  if ($_POST['Page_Number'] != '') {echo htmlspecialchars($_POST['Page_Number']);}
					  }}
					?>
					
				</div>
			</section>
		
			<section id="right">
				<p>Welcome to the Tenrikyo: Anecdotes of Oyasama Web App! The purpose of this tool is to help you find the specific anecdote that you're looking for without opening the book or consulting your local Reverend.</p>
				<p>Please note that everything here is an unofficial translation of the actual anecdotes so there may be some inconsistencies.</p>
			</section>
		
			
			<!-- I will probably create a new section down here for the Search Results to Populate after making the database call -->
		</div>
		<div id="results">
			<?php
					  // 2. Perform database query
					  
					  //if ($_SERVER["REQUEST_METHOD"] == 'POST'){
					  //if($queryStop == true) {
					  if(empty($_POST)) {
					  echo "";} elseif($queryStop == true){
					  $query = "SELECT TITLE, CONTENT, PAGE_ENTRY, COMMENTS FROM anecdotes WHERE CONTENT like '%BBQ%';" ;} 
					  elseif(isset($_POST['viewAll'])){
					  $query = "SELECT TITLE, CONTENT, PAGE_ENTRY, COMMENTS FROM anecdotes ORDER BY ID ASC;";
					  $result = mysqli_query($connection, $query);
					  }

					  else {
					  $query = "SELECT TITLE, CONTENT, PAGE_ENTRY, COMMENTS ";
					  $query .= "FROM anecdotes ";
					  // The WHERE clause needs to use elements from the FORM
					  // Need to figure out what to do with the OR statements...
					  if(strlen($_POST['Title'])>0 || strlen($_POST['Content'])>0 || strlen($_POST['Story_Number'])>0 || strlen($_POST['Page_Number'])>0){$query .= "WHERE ";}
					  if(strlen($_POST['Story_Number'])>0){$query .= "STORY_NUMBER='". $_POST['Story_Number'] . "' ";}
					  if(strlen($_POST['Title'])>0){$query .= "TITLE like '%". $_POST['Title'] . "%' ";}
					  if(strlen($_POST['Content'])>0){$query .= "CONTENT like '%". $_POST['Content'] . "%' ";}
					  if(strlen($_POST['Page_Number'])>0){$query .= "PAGE_NUMBER='". $_POST['Page_Number'] . "' ";}
					  $query .= "ORDER BY ID ASC;";
					
					//trimmed query and the COUNT logic
					
					  $trimmed_query = rtrim($query, ";");
					  $query_count = "SELECT COUNT(*) as COUNT FROM (" . $trimmed_query . ") as count_table;";
					  $count_result = mysqli_query($connection, $query_count);
  
					  // You should always define your query in a separate variable
					  $result = mysqli_query($connection, $query);
					  // Test if there was a query error
					  if (!$result) {
						echo "<p><font size=\"3\" color=\"red\">Please use only ONE search field.</font></p>";
						echo "<footer>
						<p>Please note that the content of this webapp is not affiliated nor does it reflect the views, opinions, stance of Tenrikyo Church Headquarters. Also, please note that all stories are translations from <a href="."http://www.tenrikyology.com".">'www.tenrikyology.com'</a>. I DID NOT TRANSLATE these stories. These translations do not reflect the OFFICIAL STANCE of Tenrikyo Church Headquarters</p> </footer>";
						die();
					  }
					}
			?>
			<?php
			  // 3. Use returned data (if any)
			  //if ($_SERVER["REQUEST_METHOD"] == 'POST'){
			  //if($queryStop == true) {
			  if(empty($_POST)) {
				echo "";}
			//isset is used to check if the variable $result exists
			  elseif(!isset($result)){
				echo "";
			}
			
			//check to see if 'viewAll' is not set and if so, fetch results with count
			  elseif(!isset($_POST['viewAll'])){
				while($row = mysqli_fetch_assoc($count_result)){
				
					if($row['COUNT']==1){
					echo "<em> Your search returned " . $row['COUNT'] . " result</em>" . "</br></br>";
					echo "<HR COLOR='green' WIDTH='80%'>";
					}
					
					if($row['COUNT']>1){
					echo "<em> Your search returned " . $row['COUNT'] . " results</em>" . "</br></br>";
					echo "<HR COLOR='green' WIDTH='80%'>";
					}
				}

				while($row = mysqli_fetch_assoc($result)) {
			  // ouput data from each row
			  //var_dump($row); //Note that this will dump everything onto the display
				echo "</br>" . "Title: " . $row["TITLE"] . "<br/><br/>";
				echo "Content: " . $row["CONTENT"] . "<br/><br/>";
				echo "Page Number: " . $row["PAGE_ENTRY"] . "<br/>";
				echo "<br/>";
				echo "Comments: " . $row["COMMENTS"] . "<br/><br/>";
				echo "<HR COLOR='green' WIDTH='80%'>";
				 }
				}
			
			//if 'viewAll' IS selected, returning the results without the count
			  else{
				echo "<p><font size=\"3\" color=\"blue\"><em>Returning ALL anecdotes. Happy Reading!</em></font><br>";
				while($row = mysqli_fetch_assoc($result)) {
				echo "</br>" . "Title: " . $row["TITLE"] . "<br/><br/>";
				echo "Content: " . $row["CONTENT"] . "<br/><br/>";
				echo "Page Number: " . $row["PAGE_ENTRY"] . "<br/>";
				echo "<br/>";
				echo "Comments: " . $row["COMMENTS"] . "<br/><br/>";
				echo "<HR COLOR='green' WIDTH='80%'>";
				 }
			  }
			?>
	</div>
	<footer>
			<p>Please note that the content of this webapp is not affiliated nor does it reflect the views, opinions, stance of Tenrikyo Church Headquarters. Also, please note that all stories are translations from <a href="http://www.tenrikyology.com">'www.tenrikyology.com'</a>. I DID NOT TRANSLATE these stories. These translations do not reflect the OFFICIAL STANCE of Tenrikyo Church Headquarters</p>
	</footer>
	</div>
	
  </body>
</html>
<?php
  // 4. Release returned data
  if(empty($_POST)) {
	  echo "";}
  elseif(!isset($result)){
	"";
	}
  else {
    mysqli_free_result($result);
    }
?>
<?php
  // 5. Close database connection
  mysqli_close($connection);
?>