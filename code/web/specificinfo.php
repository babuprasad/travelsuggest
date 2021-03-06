<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Travel Media</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />

</head>
<body>


<?
include 'example.php';
include 'relatedsearch.php';
ini_set('display_errors', '1');
$term=$_GET["activity"];
$city=$_GET["city"];
$activity=$_GET["activity_name"];

session_start();
$reqObj= unserialize($_SESSION[$term]);
//echo 'deeshen';
//var_dump($reqObj);

//echo $reqObj->id;
//echo $reqObj->weight;
$id = $reqObj->id;
$weight = $reqObj->weight;
$localWeight = $reqObj->localWeight;

$reqOtValue=$reqObj->outputValue;
$reqOtCat=$reqObj->outputCategory;

$storeInd=-1;

foreach($reqOtValue as $index=>$responseValue){
  if(strcmp($reqOtCat[$index],"store")==0){
    $storeInd=$index;             
  }
}

$geoterm=array($activity);
//echo $activity;

//echo $storeInd;
if($storeInd!=-1){
  $response_geo=getNearByResults($id,$city,$geoterm, $reqOtValue[$storeInd],25);  
  //$searchfield = array("eat");
  //$store = "35.06759,-106.58603";
  //$title = "Albuquerque";
  //$dist = 5;
  //$response_geo=getNearByResults($title,$searchfield, $store,$dist);
//echo 'deeshen';  
	//print_r($response_geo);
}

$requestObject = new QueryPacket();			
$requestObject->id = $id;
$requestObject->inputCategory[0] = "city";	
$requestObject->inputValue[0] = $city;
$requestObject->inputCategory[1] = "name";	
$requestObject->inputValue[1] = $term;
$requestObject->expectedResponse[0] = "name";	
$requestObject->expectedResponse[1] = "displaycontent";	
$requestObject->weight = $weight;
$requestObject->localWeight = $localWeight;
$responseObjectArray=queryProcessing($requestObject,"",false,true);


/*Most popular*/
$requestformostpop = new QueryPacket();

$requestformostpop->inputCategory[0] = "city";
$requestformostpop->inputValue[0] = $city;
$requestformostpop->inputCategory[1] = "activity";
$requestformostpop->inputValue[1] = $activity;
$requestformostpop->expectedResponse[0] = "name";
$requestformostpop->expectedResponse[1] = "displaycontent";
$respObArrformostpop=queryProcessing($requestformostpop, "activity",false,true);

//var_dump($respObArrformostpop);
$resutformorpop=$respObArrformostpop[$activity];
//var_dump($resutformorpop);

$res=$responseObjectArray[0];

$content;

$outputCatArr=$res->outputCategory;
$outputValArr=$res->outputValue;
$valInd;
$descInd;
foreach($outputValArr as $index=>$responseValue){
	if(strcmp($outputCatArr[$index],"displaycontent")==0){
		$descInd=$index;		
		break;					
	}
}

$disp_res='';
$single=$outputValArr[$descInd];	
for($out=0;$out<count($single);$out++){
$disp_res=$disp_res.' '.$single[$out];
}
$content=$disp_res;

$yelp_resp=getYelp($term,$city);
					
$src;
$rating;
$url;
$phone;
$review;
$address;
$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($yelp_resp, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
	if(strcmp($key,"image_url")==0){
		$src=$val;
	}
	if(strcmp($key,"rating_img_url")==0){
		$rating=$val;
	}
	if(strcmp($key,"url")==0){
		$url=$val;
	}
	if(strcmp($key,"phone")==0){
		$phone=$val;
	}
	if(strcmp($key,"snippet_text")==0){
		$review=$val;
	}
	if(strcmp($key,"display_address")==0){
		$address=$val;
	}
}

?>
<div id="content">
  <div id="left">
    <div id="header">
      <h1><a href="http://localhost/IR/web">Travel Media</a></h1>
      <p>Get to your favorite destination places and enjoy. Specify your preference and roam around the world</p>
      <ul id="tablist">
        
      </ul>
    </div>
    <div class="border">
      <div class="subheader">
        <p>Your desired destination is shown below. You can get the <span class="highlight">reviews, rating, how to reach there.</span>
      </div>
    </div>
    <div class="left_articles">
      <h2><?echo $term;?></h2>
      <p class="date">Powered by Yelp</p>
      <img class="bigimage" src="<?echo $src;?>" alt="Big Image" />
	<p>Content: <?echo $content;?></p>
      	<p>URL: <?echo $url;?></p>
      	<p>Reviews: <?echo $review;?></p>
      	<p>Phone: <?echo $phone;?></p>
	<P>Address: <?echo array_shift($address);?></p>
	<p>Ratings: <img src="<?echo $rating;?>" alt="Big Image" /></p>		
    </div>
    

<?

if(!empty($response_geo)) {

        $result = json_decode($response_geo,true);
      
	$jsonIterator = new RecursiveIteratorIterator(
	new RecursiveArrayIterator(json_decode($result, TRUE)),
	RecursiveIteratorIterator::SELF_FIRST);

	$geospatobj=NULL;
	foreach ($jsonIterator as $key => $val) {
		if(strcmp($key,"docs")==0){
			$geospatobj=$val;
			break;
		}
	}

	if(is_null($geospatobj)||empty($geospatobj)){

	}
	else{
?>
    <div class="left_box">
      <p>Places that are within 25 kms of <?echo $term;?>:</p>
    </div>
<?
		for($ind=0;$ind<count($geospatobj)&&$ind<3;$ind++){  
			$ind_obj=$geospatobj[$ind];
			$ind_d=$ind_obj["displaycontent"];
			$showdes='';
			for($i=0;$i<count($ind_d);$i++){
				$showdes=$showdes.' '.$ind_d[$i];
			}
			$closedist=$ind_d[count($ind_d)-1];
				
    ?>
	<div class="thirds">
     		<p><b><?echo $ind_obj["name"];?></b></br>
        	<?echo $showdes;?><?echo '</br><b>Distance: '.round($ind_obj['closedist']).' kms</b>';?></p>
    	</div>

    <?
	}
  	}
	}
    ?>
    <!--<div class="left_box">
      <p>Recently viewed places:</p>
    </div>

    <div class="thirds">
     		<p><b>Subway</b></br>
        	</p>
    	</div>
	<div class="thirds">
     		<p><b>Junction City</b></br>
        	</p>
    	</div>
	<div class="thirds">
     		<p><b>Hollister</b></br>
        	</p>
    	</div>-->

  </div>
  <div id="right">
    <div class="button">
        <a href="mapcity.php?place=<?echo array_shift($address);?>" target="_blank"><span class="big">Explore</br></span><?echo $term;?></br>
        <p>on Map</br></p></br></a>
    </div>
    <p><b><?if(count($resutformorpop)!=0){?>MOST POPULAR PLACES<?}?></b></p>
<?
$cou=0;
for($z=0;$z<count($resutformorpop);$z++){
	if($cou==3){
		break;
	}	
	if(strcmp($resutformorpop[$z]->id,$res->id)!=0){
	$cou++;
	$outputmostCatArr=$resutformorpop[$z]->outputCategory;
	//var_dump($outputmostCatArr);
	$outputmostValArr=$resutformorpop[$z]->outputValue;
	$valInd;
	$descInd;

	foreach($outputmostValArr as $index=>$responseValue){
		if(strcmp($outputmostCatArr[$index],"name")==0){
			$valInd=$index;							
		}
		if(strcmp($outputmostCatArr[$index],"displaycontent")==0){
			$descInd=$index;							
		}
	}

	$disp_res='';
	$single=$outputmostValArr[$descInd];	
	for($out=0;$out<count($single);$out++){
	$disp_res=$disp_res.' '.$single[$out];
	}
	$content=$disp_res;


?>
    <p><b><?echo $outputmostValArr[$valInd];?></b><br/>
      <?echo $content;?></p>
    <br/>
    
<?
}
}
?>
  </div>
  <div id="footer">
    <p class="right">&copy; 2013 Travel Media, Design: badk - Anirudh Karwa, Babu Prasad, Deeshen Shah, Kalpesh Kagresha.</p>
    
  </div>
</div>
</body>
</html>
