## Bus Assistant Back End

This contains the server powering the Bus Assistant for Christchurch app. This container uses [APIEngine](https://github.com/Senyeah/APIEngine) to route requests.

## Paths

##### `/version.php`

Returns the version of the database, most likely being `2018-03-18-00-30` unless I can be bothered updating it.

##### `/latest.php`

Returns a zip archive containing the SQLite database the app requires.

##### `/trip_planner.php`

Performs a trip planner request.

###### Parameters

| GET Parameter | Explanation |
| ------------- | ----------- |
| `start`, `dest` | Coordinate in decimal `latitude,longitude` format. |
| `date` | Trip date in `YYYY-MM-DD` format |
| `time` | Trip time in 24-hour (`HH:mm`) time |

###### Response

A JSON-encoded object of the form `[trip1, trip2, trip3…]`, where each trip is a different journey from `start` to `dest`. The response will contain 0 elements if no trip can be found.

Each trip as the following structure:

| Key | Explanation |
| --- | ----------- |
| `start_time` | Trip start time in `YYYY-MM-DD HH:mm:ss` format |
| `finish_time` | Trip finish time in `YYYY-MM-DD HH:mm:ss` format |
| `duration` | Trip duration, in minutes |
| `walk_time` | Time spent walking on trip, in minutes |
| `transit_time` | Time spent actively in a bus, in minutes|
| `segment` | One or more `segment` objects |

Each trip is composed of one or more _segments_ between different locations, and is either on a bus or walking. Each segment has the following structure:

| Key | Description |
| --- | ----------- |
| `type` | `bus` if this segment is on a bus, `walk` if this segment is walking |
| `trip_id` | The trip_id for this segment. This key is only set if `type` == `bus`. |
| `start_time` | Segment start time, in `YYYY-MM-DD HH:mm:ss format` |
| `finish_time` | Segment finish time, in `YYYY-MM-DD HH:mm:ss format` |
| `duration` | Segment duration, in minutes |
| `from` | An object containing: `lat`: Segment start latitude, `lon`: Segment start longitude, `stop`: If segment starts at a bus stop, this is its stop code. Not set otherwise. |
| `to` | An object containing: `lat`: Segment end latitude, `lon`: Segment end longitude, `stop`: If segment finishes at a bus stop, this is its stop code. Not set otherwise. |
| `points` | An array containing one or more `[latitude, longitude]` arrays |
