<!DOCTYPE html>
<html>

<head>
    <title>Submit Date Range</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function addDateRange() {
            const dateRangeContainer = document.getElementById('date-range-container');
            const dateRangeTemplate = `
                <div class="date-range">
                    <label for="start_date[]">Start Date:</label>
                    <input type="date" name="start_date[]" required>
                    <label for="end_date[]">End Date (optional, leave empty for single day):</label>
                    <input type="date" name="end_date[]">
                </div>
            `;
            dateRangeContainer.insertAdjacentHTML('beforeend', dateRangeTemplate);
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Submit Date Range</h1>
        <form action="submit.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <div id="date-range-container">
                <div class="date-range">
                    <label for="start_date[]">Start Date:</label><br>
                    <input class="margin-top" type="date" name="start_date[]" required><br>
                    <label for="end_date[]">End Date (optional, leave empty for single day):</label><br>
                    <input class="margin-top" type="date" name="end_date[]">
                </div>
            </div>
            <button type="button" onclick="addDateRange()">Add Another Date Range</button>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>

</html>