<link rel="stylesheet" href="global.css">
<link rel="stylesheet" href="reset.css">
<title>Priority (Non-preemptive)</title>

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


<script type="text/javascript">
        function handleSelect(elm)
        {
            window.location = elm.value+".php";
        }
</script>
<?php

function npp($arrivalTime, $burstTime, $priorities) {
    // Assume a default priority value of 1 for empty priorities
    $priorities = array_map(function ($priority) {
        return empty($priority) ? 1 : $priority;
    }, $priorities);

    // Validate that all three arrays have the same number of elements when priorities are not empty
    if (!empty($priorities) && (count($arrivalTime) !== count($burstTime) || count($burstTime) !== count($priorities))) {
        die("Error: Number of elements in Arrival Time, Burst Time, and Priorities should match.");
    }

    $processesInfo = array_map(function ($item, $index) use ($arrivalTime, $burstTime, $priorities) {
        return [
            'job' => strtoupper(base_convert($index + 10, 10, 36)),
            'at' => $item,
            'bt' => $burstTime[$index],
            'priority' => $priorities[$index],
        ];
    }, $arrivalTime, array_keys($arrivalTime));
    
    usort($processesInfo, function ($process1, $process2) {
        if ($process1['at'] > $process2['at']) return 1;
        if ($process1['at'] < $process2['at']) return -1;
        if ($process1['priority'] > $process2['priority']) return 1;
        if ($process1['priority'] < $process2['priority']) return -1;
        return 0;
    });

    $finishTime = [];
    $ganttChartInfo = [];

    $solvedProcessesInfo = [];
    $readyQueue = [];
    $finishedJobs = [];

    foreach ($processesInfo as $i => $process) {
        if ($i === 0) {
            $readyQueue[] = $process;
            $finishTime[] = $process['at'] + $process['bt'];
            $solvedProcessesInfo[] = [
                'job' => $process['job'],
                'at' => $process['at'],
                'bt' => $process['bt'],
                'priority' => $process['priority'],
                'ft' => $finishTime[0],
                'tat' => $finishTime[0] - $process['at'],
                'wat' => $finishTime[0] - $process['at'] - $process['bt'],
            ];

            foreach ($processesInfo as $p) {
                if ($p['at'] <= $finishTime[0] && !in_array($p, $readyQueue)) {
                    $readyQueue[] = $p;
                }
            }

            array_shift($readyQueue);
            $finishedJobs[] = $process;

            $ganttChartInfo[] = [
                'job' => $process['job'],
                'start' => $process['at'],
                'stop' => $finishTime[0],
            ];
        } else {
            if (empty($readyQueue) && count($finishedJobs) !== count($processesInfo)) {
                $unfinishedJobs = array_filter($processesInfo, function ($p) use ($finishedJobs) {
                    return !in_array($p, $finishedJobs);
                });
                usort($unfinishedJobs, function ($a, $b) {
                    if ($a['at'] > $b['at']) return 1;
                    if ($a['at'] < $b['at']) return -1;
                    if ($a['priority'] > $b['priority']) return 1;
                    if ($a['priority'] < $b['priority']) return -1;
                    return 0;
                });
                $readyQueue[] = $unfinishedJobs[0];
            }

            $rqSortedByPriority = $readyQueue;
            usort($rqSortedByPriority, function ($a, $b) {
                if ($a['priority'] > $b['priority']) return 1;
                if ($a['priority'] < $b['priority']) return -1;
                if ($a['at'] > $b['at']) return 1;
                if ($a['at'] < $b['at']) return -1;
                return 0;
            });

            $processToExecute = $rqSortedByPriority[0];
            $previousFinishTime = end($finishTime);

            if ($processToExecute['at'] > $previousFinishTime) {
                $finishTime[] = $processToExecute['at'] + $processToExecute['bt'];
                $newestFinishTime = end($finishTime);
                $ganttChartInfo[] = [
                    'job' => $processToExecute['job'],
                    'start' => $processToExecute['at'],
                    'stop' => $newestFinishTime,
                ];
            } else {
                $finishTime[] = $previousFinishTime + $processToExecute['bt'];
                $newestFinishTime = end($finishTime);
                $ganttChartInfo[] = [
                    'job' => $processToExecute['job'],
                    'start' => $previousFinishTime,
                    'stop' => $newestFinishTime,
                ];
            }

            $solvedProcessesInfo[] = [
                'job' => $processToExecute['job'],
                'at' => $processToExecute['at'],
                'bt' => $processToExecute['bt'],
                'priority' => $processToExecute['priority'],
                'ft' => $newestFinishTime,
                'tat' => $newestFinishTime - $processToExecute['at'],
                'wat' => $newestFinishTime - $processToExecute['at'] - $processToExecute['bt'],
            ];

            foreach ($processesInfo as $p) {
                if ($p['at'] <= $newestFinishTime && !in_array($p, $readyQueue) && !in_array($p, $finishedJobs)) {
                    $readyQueue[] = $p;
                }
            }

            $indexToRemove = array_search($processToExecute, $readyQueue);
            if ($indexToRemove !== false) {
                array_splice($readyQueue, $indexToRemove, 1);
            }

            $finishedJobs[] = $processToExecute;
        }
    }

    usort($solvedProcessesInfo, function ($obj1, $obj2) {
        if ($obj1['at'] > $obj2['at']) return 1;
        if ($obj1['at'] < $obj2['at']) return -1;
        if ($obj1['job'] > $obj2['job']) return 1;
        if ($obj1['job'] < $obj2['job']) return -1;
        return 0;
    });

    $averageTAT = array_sum(array_column($solvedProcessesInfo, 'tat')) / count($solvedProcessesInfo);
    $averageWT = array_sum(array_column($solvedProcessesInfo, 'wat')) / count($solvedProcessesInfo);

    return ['solvedProcessesInfo' => $solvedProcessesInfo, 'ganttChartInfo' => $ganttChartInfo, 'averageTAT' => $averageTAT, 'averageWT' => $averageWT];
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $arrivalTime = preg_split('/[\s,]+/', $_POST['arrivalTime']);
    $burstTime = preg_split('/[\s,]+/', $_POST['burstTime']);
    $priorities = preg_split('/[\s,]+/', $_POST['priorities']);

    // Convert the values to integers
    $arrivalTime = array_map('intval', $arrivalTime);
    $burstTime = array_map('intval', $burstTime);

    // Assume a default priority value of 1 for empty priorities
    $priorities = array_map(function ($priority) {
        return empty($priority) ? 1 : $priority;
    }, $priorities);

    // Call the scheduling function
    $result = npp($arrivalTime, $burstTime, $priorities);

    // Clear input values after submitting
    $arrivalTime = [];
    $burstTime = [];
    $priorities = [];

} else {
    // Set default values for input bars
    $arrivalTime = [];
    $burstTime = [];
    $priorities = [];
}
?>

<!-- HTML form for user input -->
<form method="post" action="" class="form-container table-border">
    <h2>NPP Scheduler</h2><br>
    <label for="arrivalTime">Arrival Time:</label>
    <input type="text" name="arrivalTime" value="<?= implode(',', $arrivalTime) ?>" required>

    <label for="burstTime">Burst Time:</label>
    <input type="text" name="burstTime" value="<?= implode(',', $burstTime) ?>" required>

    <label for="priorities">Priorities:</label>
    <input type="text" name="priorities" value="<?= implode(',', $priorities) ?>">

    <button type="submit" class="npp-btn">Submit</button>
</form>

<!-- Display the scheduling results -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div class="table-container table-border">';
    echo '<table>';
    echo '<h2>Priority (Non-preemptive)</h2>';
    echo '<tr><th>Job</th><th>Arrival Time</th><th>Burst Time</th><th>Finish Time</th><th>Turnaround Time</th><th>Waiting Time</th></tr>';
    foreach ($result['solvedProcessesInfo'] as $process) {
        echo '<tr>';
        echo '<td>' . $process['job'] . '</td>';
        echo '<td>' . $process['at'] . '</td>';
        echo '<td>' . $process['bt'] . '</td>';
        echo '<td>' . $process['ft'] . '</td>';
        echo '<td>' . $process['tat'] . '</td>';
        echo '<td>' . $process['wat'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<p class="muted">Average Turnaround Time: ' . $result['averageTAT'] . '</p>';
    echo '<p class="muted">Average Waiting Time: ' . $result['averageWT'] . '</p>';
    echo "</div>";

}
?>