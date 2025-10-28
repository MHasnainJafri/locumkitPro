<!DOCTYPE html>
<html>
<head>
    <title>Latest Industry News</title>
    <style>
        /* Fallback font and padding adjustments for better compatibility */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        h1 {
            font-size: 24px;
            color: #0056b3;
            margin-bottom: 20px;
        }
        p, div {
            font-size: 16px;
            line-height: 1.6;
        }
        a {
            color: #0056b3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .news-description {
            margin: 20px 0;
        }
        .read-more-btn {
            display: inline-block;
            background-color: #0056b3;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .read-more-btn:hover {
            background-color: #003d7a;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>{{ $news['title'] }}</h1>
        {{-- Uncomment the line below if you want to include the image --}}
        {{-- <img src="{{ asset($news['image_path']) }}" alt="{{ $news['title'] }}" style="max-width: 100%; border-radius: 5px; margin-bottom: 20px;"> --}}
        <div class="news-description">{{ Str::limit($news['description'], 50) }}</div>
        <p>Read more: <a href="{{ url('news/' . $news['slug']) }}">Click here</a></p>
        <a class="read-more-btn" href="{{ url('news/' . $news['slug']) }}">Read More</a>
    </div>
</body>
</html>
