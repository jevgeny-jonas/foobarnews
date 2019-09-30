@if (isset($news['word_stats']))
  <div>
    <h3>{{ __('Word statistics') }}:</h3>
    @foreach ($news['word_stats'] as $word => $count)
      <p>{{ $word }}: {{ $count }}</p>
    @endforeach
  </div>
@endif

@foreach ($news['items'] as $item)
  <div class="item">
    <h2><a href="{{ $item['permalink'] }}" target="_blank" rel="noopener">{!! $item['title'] !!}</a></h2>
    <p>{!! $item['description'] !!}</p>
  </div>
@endforeach
