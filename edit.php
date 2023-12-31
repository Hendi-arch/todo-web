<?php
define('REDIRECT_URL', 'Location: index.php');
session_start(); // Start the session
$taskId = $_GET['id'] ?? null;

if ($taskId !== null) {
    // Retrieve the original tasks array from the session
    $tasks = $_SESSION['tasks'];

    if (isset($tasks[$taskId])) {
        $taskToEdit = $tasks[$taskId];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle task editing
            $newTask = $_POST['task'];
            $newCategory = $_POST['category'];
            $newDueDate = $_POST['due_date'];
            $completed = isset($_POST['completed']);

            // Update the task properties
            $tasks[$taskId]['name'] = $newTask;
            $tasks[$taskId]['category'] = $newCategory;
            $tasks[$taskId]['due_date'] = $newDueDate;
            $tasks[$taskId]['completed'] = $completed;

            // Update the tasks array in the session
            $_SESSION['tasks'] = $tasks;

            header(REDIRECT_URL);
            exit();
        }
    } else {
        header(REDIRECT_URL);
        exit();
    }
} else {
    header(REDIRECT_URL);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Edit Task</title>
</head>

<body>
    <div class="centered-content">
        <h1>Edit Task</h1>
        <table class="centered-table">
            <form action="edit.php?id=<?php echo $taskId; ?>" method="post">
                <tr>
                    <td><input type="text" name="task" value="<?php echo $taskToEdit['name']; ?>" required></td>
                    <td><input type="text" name="category" value="<?php echo $taskToEdit['category']; ?>" required></td>
                    <td>
                        <label for="due_date">Due Date:</label>
                        <input type="date" name="due_date" value="<?php echo $taskToEdit['due_date']; ?>" required>
                    </td>
                    <td>
                        <label for="completed">Completed:</label>
                        <input type="checkbox" name="completed" id="completed" <?php echo $taskToEdit['completed'] ? 'checked' : ''; ?>>
                    </td>
                    <td><button type="submit" class="button-primary">Save Changes</button></td>
                </tr>
            </form>
        </table>
    </div>
</body>

</html>
