@php
    use Illuminate\Support\Str;

    $record = $getRecord();
    $field  = $field ?? 'banner';
    $val    = data_get($record, $field);

    $url = null;
    if (is_string($val) && $val !== '') {
        $url = Str::startsWith($val, ['http://', 'https://', '//'])
            ? $val
            : route('media.show', ['path' => ltrim($val, '/')]); 
    }
@endphp

@if ($url)
  <div>
    @if ($getLabel())
      <div class="text-sm text-gray-500 mb-1">{{ $getLabel() }}</div>
    @endif
    <img src="{{ $url }}" class="rounded-xl border h-40 object-cover"/>
  </div>
@endif
