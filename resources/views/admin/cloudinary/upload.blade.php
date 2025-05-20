<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload to Cloudinary</title>
</head>

<body>

    <h1>Upload Image to Cloudinary</h1>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
        <img src="{{ session('image_url') }}" alt="Uploaded Image" width="300" />
    @endif

    <form action="{{ route('api.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">Choose an image to upload:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload Image</button>
    </form>

</body>

</html>
