@foreach($users as $key => $value)
    @if(in_array($value->id, $arr_ids))
        <tr>
            <td class="text-center"> {{$value->id ?? ''}} </td>
            <td class="text-center"> {{$value->GetLeaveReport?->created_at ? \Carbon\Carbon::parse($value->GetLeaveReport->created_at)->format('d/m/y') : '' }}</td>
            
            <td class="text-center"> {{$value->firstname ?? ''}} {{ $value->lastname ?? '' }} </td>
            <td class="text-center"> {{$value->email ?? ''}} </td>
            @php
                // Check if GetLeaveReport is not null and user_reason_to_leave is available
                $array = isset($value->GetLeaveReport) ? explode('|', $value->GetLeaveReport->user_reason_to_leave) : [];
                $reason = [];
            @endphp
            <td class="">
                @if(!empty($array))
                    @foreach($array as $keys => $arrayVal)
                        {{ $keys + 1 }} {{ $arrayVal }} <br>
                    @endforeach
                @endif
            </td>
        </tr>
        <input type="hidden" name="reason[]" value="{{ json_encode($array) }}">
        <input type="hidden" name="user_id[]" value="{{$value->id ?? ''}}">
        <input type="hidden" name="leave_date[]" value="{{$value->GetLeaveReport->created_at ?? ''}}">
        <input type="hidden" name="name[]" value="{{$value->firstname ?? ''}} {{$value->lastname ?? ''}}">
        <input type="hidden" name="email[]" value="{{$value->email ?? ''}}">
    @endif
@endforeach
