<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} Blog</title>
        <link>{{ url('/blog') }}</link>
        <description>Latest posts from {{ config('app.name') }}</description>
        <language>en-us</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml"/>

        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('blog.show', $post->slug) }}</link>
            <description><![CDATA[{{ $post->excerpt ?: strip_tags($post->content) }}]]></description>
            <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
            @foreach($post->categories as $category)
            <category>{{ $category->name }}</category>
            @endforeach
        </item>
        @endforeach
    </channel>
</rss>
