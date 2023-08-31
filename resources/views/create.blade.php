<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add File</title>
</head>
<body>
    <form action="{{ route('file-items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div>
            <label for="file">File:</label>
            <input type="file" name="file" id="file" accept=".png, .jpg, .jpeg, .mp4, .geojson" required>
        </div>
        <button type="submit">Upload</button>
    </form>
    
</body>
</html>