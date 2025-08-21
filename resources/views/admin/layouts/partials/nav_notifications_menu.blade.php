<!-- Notifications: style can be found in dropdown.less -->
<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning">{{ $count_noti }}</span>
    </a>
   
    <ul class="dropdown-menu">
        @if ($count_noti === 0)
        <li class="header">You have no unread notifications</li>
        @else
        <li class="header">You have {{ $count_noti }} unread notifications</li>
        @endif
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                @foreach ($unread_noti as $noti)
                    <?php
                        if(!empty($noti->local_tran_time)){
                            $local_time = $noti->local_tran_time;
                        }else{
                            $local_time = $noti->created_at;
                        }
                    ?>
                    
                    <form method="POST" action="{{ url('admin/notifications/update/'.$noti->id) }}" id="form_noti_{{ $noti->id }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $noti->id }}">
                    </form>
                    <li>
                        <a href="#" onclick="document.getElementById('form_noti_{{ $noti->id }}').submit();">
                            <i class="fa fa-users text-aqua"></i> {!! nl2br(e($noti->description)) !!}<br>
                            <i class="fa fa-clock-o text-aqua"></i>{{ Carbon\Carbon::parse($local_time)->format('d-M-Y h:i A') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li class="footer"><a href="{{ url('admin/notifications') }}">View all</a></li>
    </ul>
</li>
