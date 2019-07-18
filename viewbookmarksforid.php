<?php
session_start();
if(isset($_SESSION['uid'])){
    include "nav1.php";
}
else{
    exit();
}

$id = $_SESSION['uid'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zomato";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT restid FROM bookmarks WHERE uid = '$id'";

$result = mysqli_query($conn, $sql);
    
$restids = array();

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        array_push($restids, (int)$row['restid']);
    }
}

mysqli_close($conn);

$restids = array_unique($restids);

//$param = urlencode(json_encode($restids));
//header('Location: mainfeature.php?data={$param}');

?>

<link rel="icon" href="images/icon.png">
<link rel="icon" href="images/icon.png">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">

<style>
    
body,h1,h2,h3,h4,h5,h6 {font-family: "Karma", sans-serif}
.w3-bar-block .w3-bar-item {
    padding:20px
}
.row{
    margin-top: 100px
}
.w3-quarter{
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 1s;
    border-radius: 15px;
}
.w3-quarter:hover {
    box-shadow: 0 8px 16px 0 rgba(2.5,2.5,0,1);
}
    button.ff {
    width: 115px;
    height: 34px;
    margin-bottom: 24px;
    background-color: #ffc107;
    border-radius: 15px;
}
     button.ff:hover {
    width: 115px;
    height: 34px;
    margin-bottom: 24px;
    background-color: black;
    color:  #ffc107;
    border-radius: 15px;
}
  
</style>
<?php include 'nav1.php';?>

<div class="container">
        <div class="row">
            <div class="col-md-4">


<?php
//if (isset($_POST['zomsubmit'])) {
//  $zomquery = $_POST['zomquery'];
//  # Continue running code
//}
//else {
//  //echo "Search key not found";
//  exit();
//}


foreach($restids as $rid){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://developers.zomato.com/api/v2.1/restaurant?res_id=".$rid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    $headers = array(
      "Accept: application/json",
      "User-Key: 15039fe16c062259656c9a018dc0dc86"
      );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    $zomdata = json_decode($result);

?>

<!--
    <div class="container">
        <div class="row">
            <div class="col-md-4">
-->

    <?php 
        if ($zomdata->thumb != "") {
          //echo "<img width='330' src='".@$restaurant->restaurant->thumb."' class='rest_image' /><br/>";
        }else{
            $zomdata->thumb = "images/abc.jpg";
        }
    ?>

                <div class="w3-main w3-content w3-padding" style="max-width:1200px;margin-top:50px">
                  <!-- First Photo Grid-->
                    <div class="w3-row-padding w3-padding-16 w3-center" id="food">
                        <div class="w3-quarter" style="width: 85%;">
                            <h3><strong><?php echo $zomdata->name; ?></strong></h3>
                            <img src="<?php echo $zomdata->thumb?>" class="restimg" alt="Sandwich" style="width:100%">
                            <?php
                                echo "<p id='".$zomdata->id."'>" 
                            ?>
                           <h4><?php  echo "User rating: ".$zomdata->user_rating->aggregate_rating."/5" ?></h4>
      <p><?php echo @$zomdata->location->locality.", ".@$zomdata->location->city." <br/>";?></p>
                              <a href="delbookmark.php?rest=<?php echo $rid; ?>"><button class="ff">Remove</button></a>
                        </div>
                      
                    </div>
                </div>
            </div>        
    <div class="col-md-4">

<?php             
}
?>
</div>
</div>
</div>
<?php include 'FooterOnly.php'; ?>
 
<script>
    $(document).ready(function(){
        $(".restimg").click(function(){
            restid = $(this).parent().find('p').attr('id');
            window.location.href = "restInfo.php?restid="+restid;
        })
    })
</script>
