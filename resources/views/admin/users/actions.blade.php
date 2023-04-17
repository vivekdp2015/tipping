<a href="{{ route('admin.users.edit', $users) }}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>
<a href="{{ route('admin.users.show', $users) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>

@if ($users->type === 'Tipper')
    <a href="{{ route('admin.users.tip_sent', $users) }}" class="btn btn-sm btn-success"><i class="fa fa-exchange"></i></a>
@elseif($users->type === 'admin')
    <button class="btn btn-sm btn-success" disabled><i class="fa fa-exchange"></i></button>
@else
    <a href="{{ route('admin.users.tip_received', $users) }}" class="btn btn-sm btn-success"><i class="fa fa-exchange"></i></a>
@endif

<a href="{{ route('admin.users.forgot_password', $users) }}" class="btn btn-sm btn-primary btn-send-password-link" title="Send Forgot Password Link"><i class="fa fa-key"></i></a>
<a href="{{ route('admin.users.delete', $users) }}" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></a>
