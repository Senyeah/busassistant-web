<?php

require_once("../resources/polyline.php");

header("Content-Type: application/json");
date_default_timezone_set("Pacific/Auckland");

$dept_time = $_GET["time"];
$dept_date = $_GET["date"];

$journey_url = "http://otp:8080/otp/routers/default/plan?fromPlace=".$_GET["from"]."&toPlace=".$_GET["to"]."&time=".$dept_time."&date=".$dept_date."&mode=TRANSIT%2CWALK&maxWalkDistance=2000.0&arriveBy=false&wheelchair=false&locale=e";

$response = json_decode(file_get_contents($journey_url), true);
$journeys = [];

function format_date($milliseconds) {
    return date("Y-m-d H:i:s", $milliseconds / 1000);
}

foreach ($response["plan"]["itineraries"] as $journey) {

    $journey_details = ["start_time" => format_date($journey["startTime"]),
                        "finish_time" => format_date($journey["endTime"]),
                        "duration" => ceil($journey["duration"] / 60),
                        "walk_time" => ceil($journey["walkTime"] / 60),
                        "transit_time" => ceil($journey["transitTime"] / 60),
                        "segments" => []];

    foreach ($journey["legs"] as $segment) {
        $segment_info = [];

        if ($segment["mode"] == "BUS") {
            $segment_info["type"] = "bus";
            $segment_info["trip_id"] = $segment["tripId"];
        } else {
            $segment_info["type"] = "walk";
        }

        $segment_info["start_time"] = format_date($segment["startTime"]);
        $segment_info["finish_time"] = format_date($segment["endTime"]);
        $segment_info["duration"] = ceil($segment["duration"] / 60);

        if (isset($segment["from"])) {
            $segment_info["from"] = ["lat" => $segment["from"]["lat"], "lon" => $segment["from"]["lon"]];

            if (isset($segment["from"]["stopCode"])) {
                $segment_info["from"]["stop"] = $segment["from"]["stopCode"];
            }
        }

        if (isset($segment["to"])) {
            $segment_info["to"] = ["lat" => $segment["to"]["lat"], "lon" => $segment["to"]["lon"]];

            if (isset($segment["to"]["stopCode"])) {
                $segment_info["to"]["stop"] = $segment["to"]["stopCode"];
            }
        }

        $polyline_points_arr = Polyline::Decode($segment["legGeometry"]["points"]);
        $polyline_points = [];

        $current_point = [];

        for ($i = 0; $i < count($polyline_points_arr); $i++) {
            if ($i % 2 == 0) {
                //lat
                $current_point[0] = $polyline_points_arr[$i];
            } else {
                //lon
                $current_point[1] = $polyline_points_arr[$i];
                $polyline_points[] = $current_point;

                $current_point = [];
            }
        }

        $segment_info["points"] = $polyline_points;
        $journey_details["segments"][] = $segment_info;
    }

    $journeys[] = $journey_details;

}

echo json_encode($journeys);

?>
