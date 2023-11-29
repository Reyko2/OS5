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

        <label for="numRequests">Number of Requests (up to 10):</label>
        <input type="number" name="numRequests" required><br>

        <div id="requestsContainer"></div>

        <button type="button" onclick="addRequest()">Add Request</button><br>

        <button type="submit" name="calculate">Calculate</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
        $currentPosition = isset($_POST["currentPosition"]) ? (int)$_POST["currentPosition"] : 0;
        $trackSize = isset($_POST["trackSize"]) ? (int)$_POST["trackSize"] : 0;
        $seekRate = isset($_POST["seekRate"]) ? (int)$_POST["seekRate"] : 0;
        $numRequests = isset($_POST["numRequests"]) ? (int)$_POST["numRequests"] : 0;

        $requests = [];
        for ($i = 1; $i <= $numRequests; $i++) {
            $requestValue = isset($_POST["request$i"]) ? (int)$_POST["request$i"] : 0;
            $requests[] = $requestValue;
        }

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

    <script>
        let requestCount = 1;

        function addRequest() {
            if (requestCount <= 10) {
                const container = document.getElementById("requestsContainer");

                const label = document.createElement("label");
                label.textContent = `Request ${requestCount}:`;

                const input = document.createElement("input");
                input.type = "number";
                input.name = `request${requestCount}`;
                input.required = true;

                container.appendChild(label);
                container.appendChild(input);
                container.appendChild(document.createElement("br"));

                requestCount++;
            } else {
                alert("Maximum limit of 10 requests reached");
            }
        }
    </script>
</body>
</html>
