<?php
function priorityScheduling($jobs)
{
    $n = count($jobs);

      // Check if any burst time is zero
      foreach ($jobs as $job) {
        if ($job['arrivalTime'] < 0 || $job['burstTime'] < 0 || $job['priority'] < 0) {
            echo "Error: Invalid input.";
            return;
        }
    }

// Sort jobs by priority only
    usort($jobs, function ($a, $b) {
      return $a['arrivalTime'] - $b['arrivalTime'];
    });


    $finishTime = array_fill(0, $n, 0);
    $turnaroundTime = array_fill(0, $n, 0);
    $waitingTime = array_fill(0, $n, 0);

    $finishTime[0] = $jobs[0]['arrivalTime'] + $jobs[0]['burstTime'];
    $turnaroundTime[0] = $finishTime[0] - $jobs[0]['arrivalTime'];
    $waitingTime[0] = $turnaroundTime[0] - $jobs[0]['burstTime'];

    for ($i = 1; $i < $n; $i++) {
        $finishTime[$i] = max($jobs[$i]['arrivalTime'], $finishTime[$i - 1]) + $jobs[$i]['burstTime'];
        $turnaroundTime[$i] = $finishTime[$i] - $jobs[$i]['arrivalTime'];
        
        // Corrected: Waiting time is the difference between Turnaround time and Burst time
        $waitingTime[$i] = $turnaroundTime[$i] - $jobs[$i]['burstTime'];

        // Ensure waiting time is non-negative
        $waitingTime[$i] = max(0, $waitingTime[$i]);
    }
    // Calculate averages
    $averageTurnaroundTime = array_sum($turnaroundTime) / $n;
    $averageWaitingTime = array_sum($waitingTime) / $n;

    // Output the table
    echo "NPP";
    echo "<table border='1'>";
    echo "<tr><th>Job</th><th>Arrival Time</th><th>Burst Time</th><th>Finish Time</th><th>Turnaround Time</th><th>Waiting Time</th><th>Priority</th></tr>";
    for ($i = 0; $i < $n; $i++) {
        echo "<tr><td>{$jobs[$i]['job']}</td><td>{$jobs[$i]['arrivalTime']}</td><td>{$jobs[$i]['burstTime']}</td><td>{$finishTime[$i]}</td><td>{$turnaroundTime[$i]}</td><td>{$waitingTime[$i]}</td><td>{$jobs[$i]['priority']}</td></tr>";
    }
    echo "</table>";

    // Output averages
    echo "<p>Average Turnaround Time: {$averageTurnaroundTime}</p>";
    echo "<p>Average Waiting Time: {$averageWaitingTime}</p>";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Priority Scheduling</title>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="arrival_times">Arrival Times:</label>
        <input type="text" name="arrival_times" required><br>

        <label for="burst_times">Burst Times:</label>
        <input type="text" name="burst_times" required><br>

        <label for="priorities">Priorities:</label>
        <input type="text" name="priorities"><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>

