<div class="editor-js-block">
    <table class="editor-js-table">
        @foreach ($content as $i => $row)
            @if ($withHeadings && $i===0)
                <thead>
                <tr>
                    @foreach ($row as $content)
                        <th>{{ $content }}</th>
                    @endforeach
                </tr>
                </thead>
            @else
                @if ($withHeadings && $i===1)
                    <tbody>
                    @endif
                    <tr>
                        @foreach ($row as $content)
                            <td>{{ $content }}</td>
                        @endforeach
                    </tr>
                    @if ($withHeadings && $loop->last)
                    </tbody>
                @endif
            @endif
        @endforeach
    </table>
</div>
