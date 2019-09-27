@foreach ($news['items'] as $item)
  <div class="item">
    <h2><a href="{{ $item['permalink'] }}" target="_blank" rel="noopener">{!! $item['title'] !!}</a></h2>
    <p>{!! $item['description'] !!}</p>
  </div>
@endforeach
