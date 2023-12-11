<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="reset.css">
    <title>Priority Scheduling</title>
</head>
<body>
<div class="form-container">
<form id="myForm">
    <label for="algorithm">Select Algorithm:</label>
    <select class="table-border" id="algorithm" name="algorithm" onchange="javascript:handleSelect(this)">
        <option value="">Select an option</option>
        <option value="SCAN">SCAN</option>
        <option value="SRTF">SRTF</option>
        <option value="NPP" selected>NPP</option>
    </select>
</form>
</div>

    <div class="form-container table-border">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="arrival_times">Arrival Times:</label>
        <input type="text" name="arrival_times" required><br>

        <label for="burst_times">Burst Times:</label>
        <input type="text" name="burst_times" required><br>

        <label for="priorities">Priorities:</label>
        <input type="text" name="priorities"><br>

        <input type="submit" value="Submit">
    </form>
    </div>

    <script type="text/javascript">
        function handleSelect(elm)
        {
            window.location = elm.value+".php";
        }
    </script>

</body>
</html>
<?php
function priorityScheduling($jobs)
{
    $n = count($jobs);

    // Sort jobs by priority and then by arrival time
    usort($jobs, function ($a, $b) {
        if ($a['priority'] == $b['priority']) {
            return $a['arrivalTime'] - $b['arrivalTime'];
        }
        return $a['priority'] - $b['priority'];
    });

    // Array initialization for storing finish, turnaround, and waiting times
    $finishTime = array_fill(0, $n, 0);
    $turnaroundTime = array_fill(0, $n, 0);
    $waitingTime = array_fill(0, $n, 0);

    $finishTime[0] = $jobs[0]['arrivalTime'] + $jobs[0]['burstTime'];
    $turnaroundTime[0] = $finishTime[0] - $jobs[0]['arrivalTime'];
    $waitingTime[0] = $turnaroundTime[0] - $jobs[0]['burstTime'];

    // Loop for iterating leftover jobs
    for ($i = 1; $i < $n; $i++) {
        $finishTime[$i] = max($jobs[$i]['arrivalTime'], $finishTime[$i - 1]) + $jobs[$i]['burstTime'];
        $turnaroundTime[$i] = $finishTime[$i] - $jobs[$i]['arrivalTime'];
        $waitingTime[$i] = $turnaroundTime[$i] - $jobs[$i]['burstTime'];
        $waitingTime[$i] = max(0, $waitingTime[$i]);
    }

    // Calculate averages
    $averageTurnaroundTime = array_sum($turnaroundTime) / $n;
    $averageWaitingTime = array_sum($waitingTime) / $n;

    // Output the table
    echo '<div class="table-container table-border">';
    echo "<h1>Non-Preemptive Priority</h1>";
    echo "<table>";
    echo "<tr><th>Job</th><th>Arrival Time</th><th>Burst Time</th><th>Finish Time</th><th>Turnaround Time</th><th>Waiting Time</th><th>Priority</th></tr>";
    for ($i = 0; $i < $n; $i++) {
        echo "<tr><td>{$jobs[$i]['job']}</td><td>{$jobs[$i]['arrivalTime']}</td><td>{$jobs[$i]['burstTime']}</td><td>{$finishTime[$i]}</td><td>{$turnaroundTime[$i]}</td><td>{$waitingTime[$i]}</td><td>{$jobs[$i]['priority']}</td></tr>";
    }
    echo "</table>";

     // Output averages
    echo '<p class="muted">Average Turnaround Time: ' . $averageTurnaroundTime . '</p>' ;
    echo '<p class="muted">Average Waiting Time: ' . $averageWaitingTime . '</p>' ;
    echo "</div>";

}

// Validate and process the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arrivalTimes = $_POST['arrival_times'];
    $burstTimes = $_POST['burst_times'];
    $priorities = $_POST['priorities'];

    // Convert input strings to arrays
    $arrivalTimes = preg_split("/[\s,]+/", $arrivalTimes);
    $burstTimes = preg_split("/[\s,]+/", $burstTimes);
    
    // If priorities input is empty, assume default values of 1 for all priorities
    if (empty($priorities)) {
        $priorities = array_fill(0, count($arrivalTimes), 1);
    } else {
        $priorities = preg_split("/[\s,]+/", $priorities);
    }

    // Validate input counts
    if (count($arrivalTimes) == count($burstTimes) && count($burstTimes) == count($priorities)) {
        $jobs = [];
        $n = count($arrivalTimes);

        for ($i = 0; $i < $n; $i++) {
            $jobs[] = [
                'job' => chr(65 + $i), // Convert to letters A, B, C
                'arrivalTime' => intval($arrivalTimes[$i]),
                'burstTime' => intval($burstTimes[$i]),
                'priority' => intval($priorities[$i]),
            ];
        }

        // Call the scheduling function
        priorityScheduling($jobs);
    } else {
        echo "Error: The number of arrival times, burst times, and priorities must be the same.";
    }
}
?>




