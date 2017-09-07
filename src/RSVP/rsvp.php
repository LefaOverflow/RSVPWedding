<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Add Guest
$app->post('/api/RSVP/AddGuest', function (Request $request, Response $response) {

$Name =     $request->getParam('Name');
$Surname =  $request->getParam('Surname');
$Cell =     $request->getParam('Cell');
$Invite =   $request->getParam('Invite');
$Status =   "N/A";

 $sql = "INSERT INTO guestlist
        (Name,Surname,Cell,InvitedTo,RSVPStatus)
        VALUES 
        (:Name,:Surname,:Cell,:Invite,:Status)";

try{
      $db = new db();
      $db = $db->connect();

      $stmt = $db->prepare($sql);

      $stmt->bindParam(':Name',         $Name);
      $stmt->bindParam(':Surname',      $Surname);
      $stmt->bindParam(':Cell',         $Cell);
      $stmt->bindParam(':Invite',       $Invite);
      $stmt->bindParam(':Status',       $Status);
      
      $stmt->execute();

      if(empty($Url))
      {
         echo '[{"notice": "Guest Successfully Added!"}]'; 
      }
      else
      {
        echo"
        <script>
	
		window.location.replace('".$Url."');

	    </script>";
      }
      
       
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
  


});
