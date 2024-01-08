<?php
// import.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded
    if (isset($_FILES["file"])) {
        $file = $_FILES["file"];

        // Check for errors during file upload
        if ($file["error"] > 0) {
            echo "Error: " . $file["error"];
        } else {
            // Process the uploaded file
            $uploadedFilePath = "uploads/" . $file["name"];
            move_uploaded_file($file["tmp_name"], $uploadedFilePath);

            // Read data from the CSV file
            $csvData = array_map('str_getcsv', file($uploadedFilePath));

            // Insert data into the database
            if (insertData($csvData)) {
                echo "Data imported successfully!";
            } else {
                echo "Error importing data.";
            }

            // Optionally, you can unlink the file after processing to remove it
            // unlink($uploadedFilePath);
        }
    } else {
        echo "No file uploaded.";
    }
} else {
    // Handle cases where the script is accessed directly
    echo "Invalid request.";
}

// Function to insert data into the database
function insertData($csvData) {
    // Assuming you have a database connection
    $db = new mysqli("your_host", "your_username", "your_password", "your_database");

    // Check the connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Loop through each row of the CSV data and insert into the database
    foreach ($csvData as $row) {
        // Assuming the CSV structure is consistent (adjust the indices accordingly)
        $id_barang = $row[0];
        $nama_barang = $row[1];
        $jumlah = $row[2];
        // ... add other fields as needed ...

        // Sanitize data before inserting into the database (to prevent SQL injection)
        $id_barang = $db->real_escape_string($id_barang);
        $nama_barang = $db->real_escape_string($nama_barang);
        $jumlah = $db->real_escape_string($jumlah);

        // Example SQL query (you need to adjust based on your database structure)
        $sql = "INSERT INTO your_table (id_barang, nama_barang, jumlah) VALUES ('$id_barang', '$nama_barang', '$jumlah')";

        // Execute the query
        if (!$db->query($sql)) {
            $db->close();
            return false; // Return false if there is an error
        }
    }

    // Close the database connection
    $db->close();
    return true; // Return true if data insertion is successful
}
?>
