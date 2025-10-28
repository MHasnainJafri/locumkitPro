@foreach($users as $key => $value)
    @if(in_array($value->id, $arr_ids))

        <tr>
            <td class="text-center"> {{ $value -> id ?? '' }} </td>
            <td class="text-center"> {{ $value -> firstname ?? ' ' }}{{ $value -> lastname ?? '' }} </td>
            <td class="text-center"> {{ $first_arr[$value -> id]['job_listing'] }} </td>
            <td class="text-center"> {{ $first_arr[$value->id]['accept_job'] }} </td>
            <td class="text-center"> {{ $first_arr[$value->id]['job_suucess_rate'] }}% </td>
            <td class="text-center"> {{ $first_arr[$value->id]['job_cancel_rate'] }}% </td>
            <td class="text-center"> {{ $first_arr[$value->id]['invitation'] }} </td>
            <td class="text-center">
                @if(isset($first_arr[$value->id]['job_listing']) && $first_arr[$value->id]['job_listing'] > 0)
                    <a href="{{ route('report.singleEmployerJobReport', ['id' => $value->id]) }}">
                        <span class="glyphicon glyphicon-eye-open" style="margin: 0 auto; display: block; color:#00A9E0; font-size: 14px;"></span>
                    </a>
                @else
                    <span class="glyphicon glyphicon-eye-open" style="margin: 0 auto; display: block; color: #ccc; font-size: 14px; cursor: not-allowed;"></span>
                @endif
            </td>

            <!--<td class="text-center"> <a href="{{route('report.singleEmployerJobReport',['id' => $value->id])}}"><span class="glyphicon glyphicon-eye-open" style="margin: 0 auto;    display: block; color:#00A9E0;    font-size: 14px;"></span></a> </td>-->
        </tr>
        <input type="hidden" name="user_id[]" value="{{$value -> id ?? ''}}">
        <input type="hidden" name="employer[]" value="{{$value -> firstname ?? ''}} {{$value -> lastname ?? ''}}">
        <input type="hidden" name="jobs_listed[]" value="{{$first_arr[$value -> id]['job_listing']}}">
        <input type="hidden" name="jobs_accepted[]" value="{{$first_arr[$value -> id]['accept_job']}}">
        <input type="hidden" name="success_rate[]" value="{{$first_arr[$value -> id]['job_suucess_rate']}}">
        <input type="hidden" name="cancel_rate[]" value="{{$first_arr[$value -> id]['job_cancel_rate']}}">
        <input type="hidden" name="private_job_sent[]" value="{{$first_arr[$value -> id]['invitation']}}">
    @endif
@endforeach