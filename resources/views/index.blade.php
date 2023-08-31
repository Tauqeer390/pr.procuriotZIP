<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File Upload</title>
</head>
<body>
    <h2>File Items</h2>
    <a href="{{ route('file-items.create') }}">Upload New File</a>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>File</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fileItems as $fileItem)
            <tr>
                <td>{{ $fileItem->title }}</td>
                <td><a href="{{ asset('storage/app/public/' . $fileItem->file_path) }}" target="_blank">View File</a></td>
                <td>
                    <a href="{{ route('file-items.edit', $fileItem->id) }}">Edit</a>
                    <form action="{{ route('file-items.destroy', $fileItem->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</body>
</html>
