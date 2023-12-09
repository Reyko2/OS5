<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="reset.css">
    <title>Menu Page</title>
    <style>
    </style>
</head>
<body>

<div class="form-container">
<form id="myForm">
    <label for="algorithm">Select Algorithm:</label>
    <select class="table-border" id="algorithm" name="algorithm" onchange="javascript:handleSelect(this)">
        <option value="">Select an option</option>
        <option value="SCAN">SCAN</option>
        <option value="SRTF">SRTF</option>
        <option value="NPP">NPP</option>
    </select>
</form>
</div>


<div id="codeContainer">
    <!-- Code will be loaded here -->
</div>

<!-- <script>
    var lastSelectedAlgorithm = "";

    function loadCode() {
        var selectedAlgorithm = document.getElementById("algorithm").value;
        var codeContainer = document.getElementById("codeContainer");

        if (selectedAlgorithm === "") {
            codeContainer.innerHTML = ""; // Clear the container if no option is selected
        } else {
            // Check if the selected algorithm is different from the last one
            if (selectedAlgorithm !== lastSelectedAlgorithm) {
                // Use AJAX to load the corresponding PHP file content
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        codeContainer.innerHTML = xhr.responseText;
                        lastSelectedAlgorithm = selectedAlgorithm; // Update the last selected algorithm
                    }
                };
                xhr.open("GET", selectedAlgorithm + ".php", true);
                xhr.send();
            }
        }
    }

    // Function to submit the form asynchronously
    function submitForm() {
        var form = document.getElementById("diskForm");
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the content with the response
                codeContainer.innerHTML = xhr.responseText;
            }
        };
        xhr.open("POST", form.action, true);
        xhr.send(formData);
        return false; // Prevent the default form submission
    }
</script> -->

<script type="text/javascript">
        function handleSelect(elm)
        {
            window.location = elm.value+".php";
        }
    </script>


</body>
</html>
