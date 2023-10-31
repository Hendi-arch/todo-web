<?php
session_start(); // Start the session

$taskId = $_POST['id'] ?? null;

if ($taskId !== null) {
    // Retrieve the original tasks array from the session
    $tasks = $_SESSION['tasks'];

    // Check if the task with the given ID exists
    if (isset($tasks[$taskId])) {
        // Remove the task with the specified ID
        unset($tasks[$taskId]);

        // Update the tasks array in the session
        $_SESSION['tasks'] = $tasks;
    }
}

header('Location: index.php');
exit();
