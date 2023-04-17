@if(!$feedbacks->status)
    <a href="{{ route('admin.feedbacks.change_status', $feedbacks) }}" class="btn btn-sm bg-green btn-change-status" title="Change Status"><i class="fa fa-check"></i></a>
@else
    <a href="{{ route('admin.feedbacks.change_status', $feedbacks) }}" class="btn btn-sm bg-maroon btn-change-status" title="Change Status"><i class="fa fa-close"></i></a>
@endif

<a href="{{ route('admin.feedbacks.show', $feedbacks) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
<a href="{{ route('admin.feedbacks.delete', $feedbacks) }}" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></a>
