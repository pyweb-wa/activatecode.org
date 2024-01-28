<?php

$counter = 0;

while (true) {
    try {
        // Your script logic goes here
        echo "Script is running. Iteration: $counter\n";

        // Simulate some work
        usleep(500000); // Sleep for 0.5 seconds (500,000 microseconds)

        // Increment the counter
        $counter++;

        // Check if the counter has reached 5
        if ($counter == 5) {
            throw new Exception("Exception triggered after 5 iterations");
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
        break; // Exit the loop on exception
    }
}

echo "Script has exited.\n";

?>
