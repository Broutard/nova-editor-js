<div class="editor-js-block">
    <table class="editor-js-embed">
        <iframe
                width="{{ isset($width) ? $width.'px' : null }}"
                height="{{ isset($height) ? $height.'px' : null }}"
                frameborder="0"
                allowfullscreen=""
                src="{{ $embed }}"></iframe>

        <div class="caption">
            {{ $caption }}
        </div>
    </table>
</div>
