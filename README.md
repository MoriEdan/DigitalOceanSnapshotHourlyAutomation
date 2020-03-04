# Simple PHP Project Which Will Automatically Take Snapshot Every Hour And Delete Old One For DigitalOcean 


###### Requirement

You will need valid DigitalOcean Token

You will need valid DigitalOcean Droplet ID

You will need to delete all current snapshots which is taken manually or using any other script or software program


###### Cronjob instruction


You need to execute snapshot_automation.php file every minute or whatever frequency you like.

I recommend using cron frequency of 10 minutes or every hour because for larger droplets it may take sometime to create snapshot.


###### Source:
https://www.ekreative.com/digital-ocean-api-daily-backups/

https://github.com/ishan3350/DigitalOceanSimpleSnapshotAPI

Thank you very much




# Daily Backups and Snapshots

[Digital Ocean](https://www.digitalocean.com/)  provides us with the ability to set up a backup of our droplets, but it can only be done weekly via their site. Handily they also enable us to create  [snapshots](https://www.digitalocean.com/community/tutorials/digitalocean-backups-and-snapshots-explained)  of a droplet at any time we want. This guide will explain how to use the Digital Ocean API to create droplet snapshots.

## Setting up snapshots with the Digital Ocean API

First we will need a security token. So go to the “API” section and click “Generate New Token”, if you have not done so already. Now you’ve got a token, lets start coding!

Be aware that we’ll need to power off the droplet in order to create our snapshot. Make sure that your server will start everything that you need after each restart.

Additionally we’ll need to get a list of existing snapshots, check their dates and delete any old snapshots.

Look at how this can be done in the code below. We use CURL, but of course you can use any library suitable to your needs.  

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

[view raw](https://gist.github.com/VovanZver/f8c9b3154a15cff50e9a/raw/0bfc7159a5f9a258a6d03b0e91e2ba3a62e07052/backup_droplet.php) [backup_droplet.php](https://gist.github.com/VovanZver/f8c9b3154a15cff50e9a#file-backup_droplet-php)  hosted with ❤ by  [GitHub](https://github.com/)

  
Then you just need to set up a cron job that will run this script.

Example for crontab: 

>  0 0 * * * /usr/bin/php /<path to script>/backup_droplet.php
    
> https://crontab.guru/#0_0_*_*_7
    
#### Cron Job Generated (you may copy & paste it to your crontab):

    0 0 * * 0 /usr/bin/php /backup_droplet.php >/dev/null 2>&1

#### Your cron job will be run at: (5 times displayed)

> -   2020-03-08 00:00:00 UTC
> -   2020-03-15 00:00:00 UTC
> -   2020-03-22 00:00:00 UTC
> -   2020-03-29 00:00:00 UTC
> -   2020-04-05 00:00:00 UTC
> -   ...

>> Free cron https://www.easycron.com/
