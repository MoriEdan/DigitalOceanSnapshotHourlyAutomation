<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;border-color:#93a1a1;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#93a1a1;color:#002b36;background-color:#fdf6e3;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#93a1a1;color:#fdf6e3;background-color:#657b83;}
.tg .tg-0lax{text-align:left;vertical-align:top}
.tg .tg-fymr{font-weight:bold;border-color:inherit;text-align:left;vertical-align:top}
.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
</style>
    <title>List of Snapshots</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <?php
$apiUrl="https://api.digitalocean.com/v2/";

$token = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$all=true;
$dropletId="XXXXXXX";//https://cloud.digitalocean.com/droplets/XXXXXXX/graphs

//listing all snapshots curl -X GET -H 'Content-Type: application/json' -H 'Authorization: Bearer b7d03a6947b217efb6f3ec3bd3504582' "https://api.digitalocean.com/v2/snapshots?page=1&per_page=1"
$ch = curl_init();
if ($all)
{
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '/snapshots?resource_type=droplet');
}
else
{
    curl_setopt($ch, CURLOPT_URL, $apiUrl . 'droplets/' . $dropletId . '/snapshots');
}
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
$response = curl_exec($ch);
//echo $response;
curl_close($ch);
$result = json_decode($response);
// echo $result;//debug
if ($result)
{
    // print_r($result->snapshots);//debug
    echo '<table class="tg">';
    echo '<tr>';
    echo '<th class="tg-0lax"><b>Snapshot ID:</b></th>';
    echo '<th class="tg-fymr"><b>Snapshot Name:</b></th>';
    echo '<th class="tg-0lax"><b>Date:</b></th>';
    echo '<th class="tg-0lax"><b>Droplet ID:</b></th>';
    echo '<th class="tg-0lax"><b>Size:</b></th>';
    echo '</tr>';

    foreach ($result->snapshots as $val)
    {
        $snapshot_id = $val->id;
        //get snapshot name
        $snapshot_name = $val->name;
        //get date
        $snap_date = $val->created_at;
        //droplet ID
        $snap_droplet = $val->resource_id;
        //get size
        $snap_size = $val->size_gigabytes;
        echo '<tr>';
        echo "<td class=\"tg-0lax\">" . $snapshot_id . "</td>"; //debug
        echo "<td class=\"tg-0lax\">" . $snapshot_name . "</td>"; //debug
        echo "<td class=\"tg-0lax\">" . $snap_date . "</td>"; //debug
        echo "<td class=\"tg-0lax\">" . $snap_droplet . "</td>"; //debug
        echo "<td class=\"tg-0lax\">" . $snap_size . " GB</td>"; //debug
        
    }
    echo '</tr>';
    echo '</table>';

}

?>
</body>
</html>
