<?php
// Function to save image from URL to local path
function saveImage($imageUrl, $localPath) {
    // Check if the URL is valid
    if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
        return "Invalid URL: $imageUrl";
    }

    // Fetch the image data from the URL
    $imageData = @file_get_contents($imageUrl);

    // Check if the image data was successfully fetched
    if ($imageData === false) {
        return "Failed to fetch the image from $imageUrl";
    }

    // Check if the directory is writable
    $directory = dirname($localPath);
    if (!is_writable($directory)) {
        return "Directory is not writable: $directory";
    }

    // Save the image data to a local file
    $result = @file_put_contents($localPath, $imageData);

    // Check if the file was successfully written
    if ($result === false) {
        echo '<pre>';
        print_r(error_get_last());
        echo '</pre>';
        return "Failed to save the image to $localPath";
    }

    return 1;
}
?>
