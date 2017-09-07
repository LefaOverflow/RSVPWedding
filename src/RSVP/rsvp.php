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

//Get All Invited Guests
$app->get('/api/RSVP/GetAllGuests', function (Request $request, Response $response) {

   $sql = "SELECT * FROM guestlist";
  
   try{
       $db = new db();
       $db = $db->connect();

       $stmt = $db->query($sql);
       $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
       $db = null;

       echo json_encode($Guests);
        
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
});

//Get All Coming Guests - BOTH
$app->get('/api/RSVP/GetAllComingGuests', function (Request $request, Response $response) {

   $sql = "SELECT * FROM guestlist WHERE RSVPStatus = 'Yes'";
  
   try{
       $db = new db();
       $db = $db->connect();

       $stmt = $db->query($sql);
       $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
       $db = null;

       echo json_encode($Guests);
        
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
});

//Get All Coming Guests - KHALL
$app->get('/api/RSVP/KHallGuests', function (Request $request, Response $response) {

   $sql = "SELECT * FROM guestlist WHERE RSVPStatus = 'Yes' AND InvitedTo = 'KHall'";
  
   try{
       $db = new db();
       $db = $db->connect();

       $stmt = $db->query($sql);
       $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
       $db = null;

       echo json_encode($Guests);
        
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
});

//Get All Coming Guests - RECEPTION
$app->get('/api/RSVP/ReceptionGuests', function (Request $request, Response $response) {

   $sql = "SELECT * FROM guestlist WHERE RSVPStatus = 'Yes' AND InvitedTo = 'Reception'";
  
   try{
       $db = new db();
       $db = $db->connect();

       $stmt = $db->query($sql);
       $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
       $db = null;

       echo json_encode($Guests);
        
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
});

//Get All Guests Not Coming
$app->get('/api/RSVP/GetNotComingGuests', function (Request $request, Response $response) {

   $sql = "SELECT * FROM guestlist WHERE RSVPStatus = 'No'";
  
   try{
       $db = new db();
       $db = $db->connect();

       $stmt = $db->query($sql);
       $Guests = $stmt->fetchAll(PDO::FETCH_OBJ);
       $db = null;

       echo json_encode($Guests);
        
   }catch(PDOException $e){
       echo '{"error": {"text": '.$e->getMessage().'}';
   }
});


//RSVP
$app->post('/api/RSVP/MakeRSVP', function (Request $request, Response $response) {

    //Checks if guest is really invited
    function isInvited($Name,$Surname,$Cell,$Invite)
    {
        $sql = "SELECT * FROM guestlist
                WHERE Name    = '$Name'
                AND   Surname = '$Surname'
                AND   Cell    = '$Cell'
                AND   InvitedTo = '$Invite'
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
                 return False; //Guest Not Invited
            }
            else
            {
                 return True; //Guest Invited
            }

        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

    }

    //Check if Guest has already RSVP'ed
    function isRSVPed($Name,$Surname,$Cell)
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
                 return False; //Guest Not Yet RSVP'ed
            }
            else
            {
                 return True; //Guest Already RSVP'ed
            }

        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
    }

    //REAL CODE BEGINS
    $Name =     $request->getParam('Name');
    $Surname =  $request->getParam('Surname');
    $Cell =     $request->getParam('Cell');
    $Invite =   $request->getParam('Invite');

     //Make the RSVP
    if(isInvited($Name,$Surname,$Cell,$Invite) == True && isRSVPed($Name,$Surname,$Cell) == False)
    {
        if($Invite == "Not Coming")
        {
           $Status =  "No";  
        }
        else
        {
           $Status =   "Yes";
        }
       
        $sql = "UPDATE guestlist SET
                Name = :Name,
                Surname = :Surname,
                Cell = :Cell,
                InvitedTo = :Invite,
                RSVPStatus = :Status
                WHERE Cell = '$Cell'"; 

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
            echo '[{"notice": "Successfully RSVPed!"}]'; 
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
    }
    elseif(isInvited($Name,$Surname,$Cell,$Invite) == False)
    {
        echo '[{"notice": "Sorry, You Are Not Invited!"}]'; 
    }
    elseif(isRSVPed($Name,$Surname,$Cell) == True)
    {
        echo '[{"notice": "Sorry, You Already RSVP-ed!"}]'; 
    }
});

