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

$num_processes = 4;
$arrival_times = array(0, 2, 4, 5);
$burst_times = array(7, 4, 1, 3);

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



?>

 <?php
function priorityScheduling($jobs)
{
    $n = count($jobs);

    // Sort jobs alphabetically
    usort($jobs, function ($a, $b) {
        return strcmp($a['job'], $b['job']);
    });

    $finishTime = array_fill(0, $n, 0);
    $turnaroundTime = array_fill(0, $n, 0);
    $waitingTime = array_fill(0, $n, 0);

    $finishTime[0] = $jobs[0]['arrivalTime'] + $jobs[0]['burstTime'];
    $turnaroundTime[0] = $finishTime[0] - $jobs[0]['arrivalTime'];
    $waitingTime[0] = $turnaroundTime[0] - $jobs[0]['burstTime'];

    for ($i = 1; $i < $n; $i++) {
        $finishTime[$i] = $finishTime[$i - 1] + $jobs[$i]['burstTime'];
        $turnaroundTime[$i] = $finishTime[$i] - $jobs[$i]['arrivalTime'];
        $waitingTime[$i] = $turnaroundTime[$i] - $jobs[$i]['burstTime'];
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

// Example usage
$jobs = [
    ['job' => 'A', 'arrivalTime' => 0, 'burstTime' => 5, 'priority' => 3],
    ['job' => 'B', 'arrivalTime' => 2, 'burstTime' => 3, 'priority' => 1],
    ['job' => 'C', 'arrivalTime' => 5, 'burstTime' => 8, 'priority' => 4],
];

priorityScheduling($jobs);

?>
