<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disk Scheduling Solver</title>
</head>
<body>
    <h1>Disk Scheduling Solver</h1>
    <form id="diskForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="currentPosition">Current Position:</label>
        <input type="number" name="currentPosition" required><br>

        <label for="trackSize">Track Size:</label>
        <input type="number" name="trackSize" required><br>

        <label for="seekRate">Seek Rate:</label>
        <input type="number" name="seekRate" required><br>

        <label for="requests">Requests (space or comma-separated):</label>
        <input type="text" name="requests" required><br>

        <button type="submit" name="calculate">Calculate</button>
    </form>

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
        $sortedRequests = $requests;
        sort($sortedRequests);

        $currentIndex = array_search($currentPosition, $sortedRequests);
        $leftRequests = array_slice($sortedRequests, 0, $currentIndex);
        $rightRequests = array_slice($sortedRequests, $currentIndex + 1);

        $leftHeadMovement = array_sum(array_map(function ($request) use ($currentPosition) {
            return abs($request - $currentPosition);
        }, $leftRequests));

        $rightHeadMovement = array_sum(array_map(function ($request) use ($currentPosition) {
            return abs($request - $currentPosition);
        }, $rightRequests));

        $totalHeadMovement = $leftHeadMovement + $rightHeadMovement;
        $seekTime = $totalHeadMovement / $seekRate;

        // Display results
        echo "<div id='result'>";
        echo "<p>Total head movement: $totalHeadMovement tracks</p>";
        echo "<p>Seek time: $seekTime seconds</p>";
        echo "</div>";
    }
    ?>
</body>
</html>
