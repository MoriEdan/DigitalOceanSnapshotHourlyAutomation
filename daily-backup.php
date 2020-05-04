<?php
class BackupDroplet
{
    private $apiUrl = 'https://api.digitalocean.com/v2/';
    private $token;
    private $dropletId;

    public function __construct($token, $dropletId)
    {

        $this->token = $token;
        $this->dropletId = $dropletId;

    }

    public function getSnapshots()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'droplets/' . $this->dropletId . '/snapshots');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response);
        if ($result) {
            return $result->snapshots;
        }
        return [];
    }

    public function powerOff()
    {
        $data = array("type" => "power_off");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'droplets/' . $this->dropletId . '/actions');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function powerOn()
    {
        $data = array("type" => "power_on");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'droplets/' . $this->dropletId . '/actions');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function checkIfOn()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'droplets/' . $this->dropletId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response);
        if (!$result) {
            return true;
        }
        if ($result->droplet->status == 'active') {
            return true;
        }
        return false;
    }

    public function createSnapshot()
    {
        $this->powerOff();
        while ($this->checkIfOn()) {
            sleep(5);
        }
        $data = array("type" => "snapshot", "name" => "name for snapshot " . date('d-M-Y H:i'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'droplets/' . $this->dropletId . '/actions');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_exec($ch);
        curl_close($ch);
    }

    public function deleteSnapshot($id)
    {
        if ($id) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . 'images/' . $id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    public function deleteOutdatedSnapshots()
    {
        $currentDate = new DateTime();
        $snapshots = $this->getSnapshots();
        foreach ($snapshots as $snapshot) {
            $snapshotDate = new DateTime($snapshot->created_at);
            $snapshotDate->modify('+7 days');

            if ($snapshotDate < $currentDate) {
                $this->deleteSnapshot($snapshot->id);
            }
        }

    }
}

$BackupDroplet = new BackupDroplet('your security token', 'droplet id');

$BackupDroplet->createSnapshot();
$BackupDroplet->deleteOutdatedSnapshots();
