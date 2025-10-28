@foreach($BlockUsers as $key => $value)
    @if(in_array($value['id'], $arr_ids))
    <tr>
        <td class="text-center"> {{$value['id']}} </td>
        <td class="text-center"> {{$value['firstname']}} </td>
        <td class="text-center"> {{$value['lastname' ]}} </td>
        <td class="text-center"> {{$value['email']}} </td>
        <td class="text-center"> {{$value['active'] == 2 ? 'blocked' : ''}} </td>

        <input type="hidden" name="user_id[]" value="{{$value['id']}}">
        <input type="hidden" name="firstname[]" value="{{$value['firstname']}}">
        <input type="hidden" name="lastname[]" value="{{$value['lastname']}}">
        <input type="hidden" name="email[]" value="{{$value['email']}}">
        <input type="hidden" name="status[]" value="{{$value['active'] == 2 ? 'Blocked' : ''}}">
    </tr>
    @endif
@endforeach