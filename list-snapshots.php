<?php
$apiUrl="https://api.digitalocean.com/v2/";

$token = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

$dropletId="XXXXXXX";//https://cloud.digitalocean.com/droplets/XXXXXXX/graphs

//listing all snapshots curl -X GET -H 'Content-Type: application/json' -H 'Authorization: Bearer b7d03a6947b217efb6f3ec3bd3504582' "https://api.digitalocean.com/v2/snapshots?page=1&per_page=1"
  $ch = curl_init();
  if($all){
  curl_setopt($ch, CURLOPT_URL, $apiUrl . '/snapshots?resource_type=droplet');
  }else{
        curl_setopt($ch, CURLOPT_URL, $apiUrl . 'droplets/' . $dropletId . '/snapshots');
    }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);
        $result = json_decode($response);
        echo $result;//debug
        if ($result) {
            print_r($result->snapshots);;//debug
                        
                        //get the snapshot id
                         $snapshot_id= $result->snapshots[0]->id; 
                         //get snapshot name
                         $snapshot_name= $result->snapshots[0]->name; 


        }
       
        
             
echo "snapshot_id:".isset($snapshot_id)."<br>\n";;//debug
echo "snapshot_name:".isset($snapshot_name);;//debug


?>
