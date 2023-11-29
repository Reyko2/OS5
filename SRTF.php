<link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="reset.css">

<?php
class Process {
    public $pid;
    public $arrival_time;
    public $burst_time;
    public $start_time;
    public $completion_time;
    public $turnaround_time;
    public $waiting_time;
    public $response_time;
 
    public function __construct($pid, $arrival_time, $burst_time) {
        $this->pid = $pid;
        $this->arrival_time = $arrival_time;
        $this->burst_time = $burst_time;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data

    // Validate the input
    $arrival_times_input = $_POST["arrival_times"];
    $burst_times_input = $_POST["burst_times"];

    $arrival_times = array_map('intval', preg_split('/[\s,]+/', $arrival_times_input));
    $burst_times = array_map('intval', preg_split('/[\s,]+/', $burst_times_input));

    // Validation: Check if burst times start with 0
    if (in_array(0, $burst_times)) {
        echo "Error: 0 is invalid burst time.";
        exit;
    }

    $num_processes = count($arrival_times);

    if ($num_processes != count($burst_times)) {
        echo "Error: Number of arrival times and burst times should be the same.";
        exit;
    }

$processes = array();
$burst_remaining = array();
$total_turnaround_time = 0;
$total_waiting_time = 0;
$total_response_time = 0;
$total_idle_time = 0;


for($i = 0; $i < $num_processes; $i++) {
    $processes[$i] = new Process($i, $arrival_times[$i], $burst_times[$i]);
    $burst_remaining[$i] = $burst_times[$i];
}

$current_time = 0;
$completed = 0;

while ($completed != $num_processes) {
    $idx = -1;
    $min_burst = INF;

    // Find the process with the shortest remaining time
    for ($i = 0; $i < $num_processes; $i++) {
        if ($processes[$i]->arrival_time <= $current_time && $burst_remaining[$i] < $min_burst && $burst_remaining[$i] > 0) {
            $min_burst = $burst_remaining[$i];
            $idx = $i;
        }
    }

    if ($idx != -1) {
        if ($burst_remaining[$idx] == $processes[$idx]->burst_time) {
            $processes[$idx]->start_time = $current_time;
            $total_idle_time += $processes[$idx]->start_time - $current_time;
        }

        $burst_remaining[$idx]--;
        $current_time++;

        if ($burst_remaining[$idx] == 0) {
            $processes[$idx]->completion_time = $current_time;
            $processes[$idx]->turnaround_time = $processes[$idx]->completion_time - $processes[$idx]->arrival_time;
            $processes[$idx]->waiting_time = $processes[$idx]->turnaround_time - $processes[$idx]->burst_time;
            $processes[$idx]->response_time = $processes[$idx]->start_time - $processes[$idx]->arrival_time;
            $total_turnaround_time += $processes[$idx]->turnaround_time;
            $total_waiting_time += $processes[$idx]->waiting_time;
            $total_response_time += $processes[$idx]->response_time;
            $completed++;
        }
    } else {
        $current_time++;
    }
}

$avg_turnaround_time = $total_turnaround_time / $num_processes;
$avg_waiting_time = $total_waiting_time / $num_processes;
$avg_response_time = $total_response_time / $num_processes;

// Output the results as needed
echo '<div class="table-container table-border">';
echo "<h1>Shortest Remaining Time First</h1>";
echo "<table>";
echo "<tr>";
echo "<th>Process</th><th>Arrival Time</th><th>Burst Time</th><th>Start Time</th><th>Completion Time</th><th>Turnaround Time</th><th>Waiting Time</th>";
echo "</tr>";

foreach ($processes as $process) {
    echo "<tr>";
    echo "<td>{$process->pid}</td>";
    echo "<td>{$process->arrival_time}</td>";
    echo "<td>{$process->burst_time}</td>";
    echo "<td>{$process->start_time}</td>";
    echo "<td>{$process->completion_time}</td>";
    echo "<td>{$process->turnaround_time}</td>";
    echo "<td>{$process->waiting_time}</td>";
    echo "</tr>";
}

echo "</table>";

// Output
echo '<p class="muted">Average Turnaround Time: ' . $avg_turnaround_time . '</p>' ;
echo '<p class="muted">Average Waiting Time: ' . $avg_waiting_time . '</p>' ;

echo "</div>";

} 
else {
// Display the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SRTF Scheduler</title>
</head>
<body>

<div class="form-container table-border">
<h2 class="left-fullw">SRTF Scheduler</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="arrival_times">Arrival Times:</label>
    <input type="text" name="arrival_times" required>
    <br>
    <label for="burst_times">Burst Times:</label>
    <input type="text" name="burst_times" required>
    <br>
    <input type="submit" value="Submit">
</form>
</div>
</body>
</html>
<?php } ?>