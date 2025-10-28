@if($users != null)
    @foreach($users as $key => $value)
        @if(count($value -> PrivateUser) > 0)
            @foreach($value -> PrivateUser as $keys => $values)

                @if(in_array($values->id, $arr_ids))
                    <tr>
                        <td class="text-center"> {{$values -> name ?? ''}} </td>
                        <td class="text-center"> {{$values -> email ?? ''}} </td>
                        <td class="text-center"> {{$value -> firstname ?? ''}} {{ $value -> lastname ?? '' }} </td>
                        <td class="text-center"> {{$value -> id ?? ''}} </td>
                        <td class="text-center"> {{$value -> user_acl_profession -> name ?? ''}} </td>
                    </tr>
                    <input type="hidden" name="name[]" value="{{$values -> name ?? ''}}">
                    <input type="hidden" name="email[]" value="{{$values -> email ??''}}">
                    <input type="hidden" name="employer_name[]" value="{{$value -> firstname ?? ''}} {{$value -> lastname ?? ''}}">
                    <input type="hidden" name="employer_id[]" value="{{$value -> id ?? ''}}">
                    <input type="hidden" name="profession[]" value="{{$value -> user_acl_profession -> name ?? ''}}">
                @endif

                
            @endforeach
        @endif
    @endforeach
@endif