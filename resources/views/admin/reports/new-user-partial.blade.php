@php
    $hasData = false;
@endphp

@foreach($users as $key => $value)
    @if(in_array($value->id , $arr_ids))
        @php $hasData = true; @endphp
        <tr>
            <td class="text-center"> {{$value->id ?? ''}} </td>
            <td class="text-center"> {{ $value->created_at ? \Carbon\Carbon::parse($value->created_at)->format('d/m/y') : '' }} </td>
            <td class="text-center"> {{$value->firstname ?? '' }} </td>
            <td class="text-center"> {{$value->lastname ?? '' }} </td>
            <td class="text-center"> {{$value->email ?? '' }} </td>
            <td class="text-center"> {{$value->user_acl_profession->name ?? '' }} </td>
            <td class="text-center"> {{$value->role->name }} </td>
            <td class="text-center">
                <label for="" style="color: green;"> {{$value->email_verified_at != null ? 'Active' : ''}} </label>
                <label for="" style="color: #855D10;"> {{$value->email_verified_at == null ? 'Guest User' : ''}} </label>
            </td>
            
            <input type="hidden" name="user_id[]" value="{{$value->id ?? ''}}">
            <input type="hidden" name="sign_up_dates[]" value="{{$value->created_at ?? ''}}">
            <input type="hidden" name="firstname[]" value="{{$value->firstname ?? ''}}">
            <input type="hidden" name="lastname[]" value="{{$value->lastname ?? ''}}">
            <input type="hidden" name="email[]" value="{{$value->email ?? ''}}">
            <input type="hidden" name="category[]" value="{{$value->user_acl_profession->name ?? ''}}">
            <input type="hidden" name="user_type[]" value="{{$value->role->name ?? ''}}">
            <input type="hidden" name="status[]" value="{{$value->email_verified_at == null ? 'Guest User' : 'Active'}}">
        </tr>
    @endif
@endforeach

@if(!$hasData)
    <tr>
        <td colspan="8" class="text-center">No data available</td>
    </tr>
@endif
