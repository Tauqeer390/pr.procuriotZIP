<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File Upload</title>
</head>
<body>
    
</body>
</html>
    <h2>Edit File Item</h2>
    <form action="{{ route('file-items.update', $fileItem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="{{ $fileItem->title }}" required>
        </div>
        <div>
            <label for="file">File:</label>
            <input type="file" name="file" id="file" accept=".png, .jpg, .jpeg, .mp4, .geojson">
        </div>
        <button type="submit">Update</button>
    </form>
