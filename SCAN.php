<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="reset.css">
    <title>Disk Scheduling Solver</title>
</head>
<body>
    <div class="form-container table-border">
    <h2 class="left-fullw">Disk Scheduling Solver</h2>
    <form id="diskForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="currentPosition">Current Position:</label>
        <input type="number" name="currentPosition" required>

        <label for="trackSize">Track Size:</label>
        <input type="number" name="trackSize" required>

        <label for="seekRate">Seek Rate:</label>
        <input type="number" name="seekRate" required>

        <label for="requests">Requests (space or comma-separated):</label>
        <input type="text" name="requests" required>
        <input type="submit" name="calculate">
    </form>
</div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
        $currentPosition = isset($_POST["currentPosition"]) ? (int)$_POST["currentPosition"] : 0;
        $trackSize = isset($_POST["trackSize"]) ? (int)$_POST["trackSize"] : 0;
        $seekRate = isset($_POST["seekRate"]) ? (int)$_POST["seekRate"] : 0;
        $requestsString = isset($_POST["requests"]) ? $_POST["requests"] : '';

        // Parse requests from the input string
        $requests = preg_split('/[\s,]+/', $requestsString, -1, PREG_SPLIT_NO_EMPTY);
        $requests = array_map('intval', $requests);

        // Perform SCAN algorithm calculation
        function SCAN($arr, $head, $direction) {
            $seek_count = 0;
            $distance = 0;
            $cur_track = 0;
            $left = [];
            $right = [];
            $seek_sequence = [];

            // Appending end values
            // which has to be visited
            // before reversing the direction
            if ($direction == "left") {
                array_push($left, 0);
            } elseif ($direction == "right") {
                array_push($right, $GLOBALS['trackSize'] - 1);
            }

            for ($i = 0; $i < count($arr); $i++) {
                if ($arr[$i] < $head) {
                    array_push($left, $arr[$i]);
                }
                if ($arr[$i] > $head) {
                    array_push($right, $arr[$i]);
                }
            }

            // Sorting left and right vectors
            sort($left);
            sort($right);

            // Run the while loop two times.
            // One by one scanning right
            // and left of the head
            $run = 2;
            while ($run-- > 0) {
                if ($direction == "left") {
                    for ($i = count($left) - 1; $i >= 0; $i--) {
                        $cur_track = $left[$i];

                        // Appending current track to seek sequence
                        array_push($seek_sequence, $cur_track);

                        // Calculate absolute distance
                        $distance = abs($cur_track - $head);

                        // Increase the total count
                        $seek_count += $distance;

                        // Accessed track is now the new head
                        $head = $cur_track;
                    }
                    $direction = "right";
                } elseif ($direction == "right") {
                    for ($i = 0; $i < count($right); $i++) {
                        $cur_track = $right[$i];

                        // Appending current track to seek sequence
                        array_push($seek_sequence, $cur_track);

                        // Calculate absolute distance
                        $distance = abs($cur_track - $head);

                        // Increase the total count
                        $seek_count += $distance;

                        // Accessed track is now new head
                        $head = $cur_track;
                    }
                    $direction = "left";
                }
            }

            // Calculate total head movement and seek time
            $totalHeadMovement = $seek_count;
            $seekTime = $totalHeadMovement / $GLOBALS['seekRate'];

            // Display results
            // start main div
            echo '<div>';
            
            // start container
            echo '<div class="scan-container table-border">';

            echo '<div class="seek-operations">
            <p class="left-fullw">Total number of seek operations</p>
            <h2 class="left-fullw">'. $seek_count. '</h2>
            </div>';

            echo '<div class="seek-operations">
            <p class="left-fullw">Total head movement</p>
            <h2 class="left-fullw">'. $totalHeadMovement. '</h2>
            </div>';

            echo '<div class="seek-operations">
            <p class="left-fullw">Seek time</p>
            <h2 class="left-fullw">'. $seekTime. '</h2>
            </div>';

            // end container        
            echo '</div>';

            // another container
            echo '<div class="scan-container table-border">';
            // echo '<div class="seek-operations">';
            echo '<p class="left-fullw">Seek Sequence is: </p>';

            for ($i = 0; $i < count($seek_sequence); $i++) {
                echo '<div class="seek-sequence">' . $seek_sequence[$i]. '</div>';
            }
            // echo '</div>';

            // end container
            echo '</div>';

            // end main div
            echo '</div>'; 
        }

        // Display results
        SCAN($requests, $currentPosition, "left");
    }
    ?>
</body>
</html>
