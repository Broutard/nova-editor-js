<div class="editor-js-block">
    <figure class="editor-js-image">
        <img src="{{ !empty($file['url']) ? $file['url'] : asset($file['path']) }}" alt="{{ e($caption) }}">
        @if (!empty($caption))
            <figcaption>{{ $caption }}</figcaption>
        @endif
    </figure>
</div>
