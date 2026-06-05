@extends('layouts.app')

@section('title', 'Notifications - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Notifications</h2>
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Mark All as Read</button>
            </form>
        </div>
        
        <div class="card">
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div class="border-bottom pb-3 mb-3 notification-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $notification->title }}</h6>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @if(!$notification->is_read)
                                <button class="btn btn-sm btn-link mark-read" data-id="{{ $notification->id }}">
                                    Mark as Read
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No notifications found.</p>
                @endforelse
                
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.mark-read').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/notifications/' + id + '/read',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            }
        });
    });
</script>
@endpush