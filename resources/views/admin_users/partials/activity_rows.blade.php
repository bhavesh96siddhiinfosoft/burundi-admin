@php
    use Illuminate\Support\Str;

    // helper to detect UUID-like strings
    function extractIdAndAction($text) {
        $text = trim($text);

        // not starting with # -> no id
        if (!Str::startsWith($text, '#')) {
            return ['id' => '-', 'action' => $text];
        }

        // starts with #[ ... ]  -> capture up to the closing bracket
        if (Str::startsWith($text, '#[')) {
            $closingPos = strpos($text, ']');
            if ($closingPos !== false) {
                $id = substr($text, 0, $closingPos + 1); // include closing ]
                $action = trim(substr($text, $closingPos + 1));
                return [
                    'id' => $id,
                    'action' => $action !== '' ? $action : '-',
                ];
            }
            // no closing bracket found -> fallback to splitting on whitespace
        }

        // default: take first whitespace-separated token as id
        $parts = preg_split('/\s+/', $text, 2);
        return [
            'id' => $parts[0],
            'action' => isset($parts[1]) && trim($parts[1]) !== '' ? $parts[1] : '-',
        ];
    }
@endphp
@foreach($activities as $activity)
    @php $parsed = extractIdAndAction($activity->action); @endphp
    <tr>
        <td class="delete-all">
            <input type="checkbox" id="is_open_{{$activity->id}}" class="activity_checkbox" data-id="{{$activity->id}}">
            <label class="col-3 control-label" for="is_open_{{$activity->id}}"></label>
        </td>
        <td>{{ optional($activity->user)->name ?? 'N/A' }}</td>
        <td>{{ $parsed['action'] }}</td>
        <td class="affected-id text-primary" style="cursor:pointer" data-id="{{ $parsed['id'] }}">
            {{ $parsed['id'] }}
        </td>
        <td>{{ $activity->created_at }}</td>
        <td class="action-btn">
            <a href="{{route('admin.activity.delete', ['id' => $activity->id])}}" class="do_not_delete">
                <i class="fa fa-trash"></i>
            </a>
        </td>
    </tr>
@endforeach
