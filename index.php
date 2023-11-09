<?php
session_start();

// Initialize tasks array if it doesn't exist in the session
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Handle task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTask'])) {
    $task = $_POST['task'];
    $category = $_POST['category'];
    $dueDate = $_POST['due_date'];

    if (!empty($task) && !empty($category)) {
        $taskId = uniqid();
        $newTask = [
            'id' => $taskId,
            'name' => $task,
            'category' => $category,
            'due_date' => $dueDate,
            'completed' => false,
        ];
        $_SESSION['tasks'][$taskId] = $newTask;

        // Redirect to the same page after successfully adding a task
        header('Location: index.php');
        exit;
    }
}

// Retrieve tasks from the session
$tasks = $_SESSION['tasks'];

// Handle sorting, filtering, and searching
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear'])) {
        // Clear sorting, filtering, and search criteria
        $sort = 'name';
        $filter = 'all';
        $searchTerm = '';
    } else {
        // Sorting
        $sort = $_POST['sort'] ?? 'name';

        // Filtering
        $filter = $_POST['filter'] ?? 'all';

        // Searching
        $searchTerm = $_POST['search'] ?? '';
    }
}

// Sort tasks
function compareTasks($a, $b)
{
    global $sort;
    return strcmp($a[$sort], $b[$sort]);
}

usort($tasks, 'compareTasks');

// Filter tasks
if ($filter === 'completed') {
    $tasks = array_filter($tasks, function ($task) {
        return $task['completed'];
    });
} elseif ($filter === 'incomplete') {
    $tasks = array_filter($tasks, function ($task) {
        return !$task['completed'];
    });
}

// Search tasks
if (!empty($searchTerm)) {
    $tasks = array_filter($tasks, function ($task) use ($searchTerm) {
        return stripos($task['name'], $searchTerm) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Task List</title>
</head>

<body>
    <div class="centered-content">
        <h1>Task List</h1>
        <table class="centered-table">
            <form action="index.php" method="post">
                <tr>
                    <td><input type="text" name="task" placeholder="Add a new task" required></td>
                    <td><input type="text" name="category" placeholder="Category" required></td>
                    <td>
                        <label for="due_date">Due Date:</label>
                        <input type="date" name="due_date" required>
                    </td>
                    <td><button type="submit" class="button-primary" name="addTask">Add Task</button></td>
                </tr>
            </form>
        </table>
    </div>

    <div class="centered-content">
        <h2>Tasks</h2>
        <form action="index.php" method="post">
            <label for="sort">Sort by:</label>
            <select id="sort" name="sort" onchange="this.form.submit();">
                <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Name</option>
                <option value="due_date" <?= ($sort === 'due_date') ? 'selected' : '' ?>>Due Date</option>
                <option value="category" <?= ($sort === 'category') ? 'selected' : '' ?>>Category</option>
            </select>
            <label for="filter">Filter:</label>
            <select id="filter" name="filter" onchange="this.form.submit();">
                <option value="all" <?= ($filter === 'all') ? 'selected' : '' ?>>All</option>
                <option value="completed" <?= ($filter === 'completed') ? 'selected' : '' ?>>Completed</option>
                <option value="incomplete" <?= ($filter === 'incomplete') ? 'selected' : '' ?>>Incomplete</option>
            </select>
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?= isset($_POST['search']) ? $_POST['search'] : '' ?>">
            <button type="submit" class="button-secondary" name="searchTask">Search</button>
            <button type="submit" class="button-secondary" name="clear">Reset</button>
        </form>
    </div>

    <ul>
        <?php foreach ($tasks as $task) : ?>
            <?php
            $completed = $task['completed'] ? '✔️' : '○';
            $dueDate = strtotime($task['due_date']);
            $today = strtotime('today');
            $daysDifference = floor(($dueDate - $today) / (60 * 60 * 24));

            $colorClass = '';
            if ($daysDifference == 0) {
                $colorClass = 'red';
            } elseif ($daysDifference < 0) {
                $colorClass = 'black';
            } elseif ($daysDifference <= 7) {
                $colorClass = 'yellow';
            } else {
                $colorClass = 'green';
            }

            $dueDateFormatted = date("F j, Y (l)", $dueDate);
            $dueDateDisplay = "<span class='$colorClass'>$dueDateFormatted</span>";
            ?>

            <li>
                <form action='check.php' method='post'>
                    <input type='hidden' name='id' value='<?= $task['id'] ?>'>
                    <button type='submit' class='button-check'>Check</button>
                    <?= "$completed {$task['name']} (Category: {$task['category']}) - Due Date: $dueDateDisplay" ?>
                </form>
                <form action='edit.php' method='get'>
                    <input type='hidden' name='id' value='<?= $task['id'] ?>'>
                    <button type='submit' class='button-edit'>Edit</button>
                </form>
                <form action='delete.php' method='post'>
                    <input type='hidden' name='id' value='<?= $task['id'] ?>'>
                    <button type='submit' class='button-delete'>Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>
