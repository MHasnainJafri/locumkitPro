@foreach($users as $key => $value)
    @if(count($value-> Private_jobs) > 0)
        @foreach($value -> Private_jobs as $keys => $values)
            @if(in_array($values->id, $arr_ids))
                <tr>
                    <td class="text-center"> {{$value -> firstname ?? ' '}} {{ $value -> lastname ?? '' }} </td>
                    <td class="text-center"> {{$value -> id ?? ''}} </td>
                    <td class="text-center"> {{$values -> emp_name ?? ''}} </td>
                    <td class="text-center"> {{$values -> job_location ?? ''}} </td>
                    <!--<td class="text-center"> �{{$values -> job_rate ?? ''}} </td>-->
                    <td class="text-center"><i class="fa fa-gbp" aria-hidden="true"></i> {{$values -> job_rate ?? ''}} </td>

                    <td class="text-center"> {{ $values -> job_date ? \Carbon\Carbon::parse($values -> job_date)->format('d/m/y') : '' }} </td>
                </tr>
                <input type="hidden" name="locum_name[]" value="{{$value->firstname ?? ''}} {{$value -> lastname ?? ''}}">
                <input type="hidden" name="locum_id[]" value="{{$value -> id ?? ''}}">
                <input type="hidden" name="employer_name[]" value="{{$values -> emp_name ?? ''}}">
                <input type="hidden" name="location[]" value="{{$values -> job_location ?? ''}}">
                <input type="hidden" name="rate[]" value="{{$values -> job_rate ?? ''}}">
                <input type="hidden" name="date[]" value="{{$values -> job_date ?? ''}}">

            @endif
        @endforeach
    @endif
@endforeach