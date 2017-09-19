<?php

require_once '../engine/runtime.php';
define('VERSION', '2017-06-28-05-00');

class LatestDatabaseRequest implements APIEngine\Requestable {

    public function execute($request) {

        header('Content-Type: application/zip');
        header('Content-Transfer-Encoding: Binary');
        header('Content-Disposition: attachment; filename="database-' . VERSION . '.zip"');
        header('Content-Length: '.filesize('../resources/database.zip'));

        readfile('../resources/database.zip');

    }

}

class TripPlannerRequest implements APIEngine\Requestable {

    public function execute($request) {
        require_once 'trip_planner.php';
    }

}

class VersionRequest implements APIEngine\Requestable {

    public function execute($request) {
        echo VERSION;
    }

}

?>
