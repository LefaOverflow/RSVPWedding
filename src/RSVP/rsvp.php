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

//RSVP
$app->post('/api/RSVP/MakeRSVP', function (Request $request, Response $response) {

    //Checks if guest is really invited
    function isInvited($Name,$Surname,$Cell)
    {
        $sql = "SELECT * FROM guestlist
                WHERE Name    = '$Name'
                AND   Surname = '$Surname'
                AND   Cell    = '$Cell'
                ";
       try
        {
            $db = new db();
            $db = $db->connect();

            $stmt = $db->query($sql);
            $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            if(empty($Guests))
            {
              echo '[{"notice": "Guest is not Invited!"}]'; 
            }
            else
            {
              echo json_encode($Guests);
            }

        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

    }

    //Check if Guest has already RSVP'ed
    function isRVSPed($Name,$Surname,$Cell)
    {
        $sql = "SELECT * FROM guestlist
                WHERE Name    = '$Name'
                AND   Surname = '$Surname'
                AND   Cell    = '$Cell'
                AND   RSVPStatus = 'Yes'
                ";
       try
        {
            $db = new db();
            $db = $db->connect();

            $stmt = $db->query($sql);
            $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            if(empty($Guests))
            {
              echo '[{"notice": "Guest hasnt RSVPed yet!"}]'; 
            }
            else
            {
              echo json_encode($Guests);
            }

        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

    }
    


});

