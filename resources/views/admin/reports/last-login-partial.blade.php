@foreach($users as $key => $value)
@if(in_array($value->id, $arr_ids))

<tr>
    <td class="text-center"> {{$value -> id ?? ''}} </td>
    <td class="text-center"> {{ $value -> GetLastloginUsers?-> login_time ? \Carbon\Carbon::parse($value -> GetLastloginUsers -> login_time)->format('d/m/y') : '' }} </td>
  
    <td class="text-center"> {{$value -> firstname ?? ''}} {{ $value -> lastname ?? '' }} </td>
    <td class="text-center"> {{$value -> email ?? ''}} </td>
    <td class="text-center"> {{$value -> user_acl_profession -> name ?? ''}} </td>
    <td class="text-center"> {{$value -> role -> name ?? ''}} </td>
</tr>
<input type="hidden" name="user_id[]" value="{{$value -> id ?? ''}}">
<input type="hidden" name="lastlogindate[]" value="{{$value -> GetlastLoginUsers -> login_time ?? ''}}">
<input type="hidden" name="name[]" value="{{$value -> firstname ?? ''}} {{$value -> lastname ?? ''}}">
<input type="hidden" name="email[]" value="{{$value -> email ?? ''}}">
<input type="hidden" name="category[]" value="{{$value -> user_acl_profession -> name ?? ''}}">
<input type="hidden" name="user_type[]" value="{{$value -> role -> name ?? ''}}">
@endif
@endforeach