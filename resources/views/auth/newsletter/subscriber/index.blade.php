@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $title or config('app.name') }}</div>

                <div class="panel-body">
                	@if ($subscribers->total() === 0)
                		<div class="alert alert-info">There is no people subscribed subscriber.</div>
                	@endif

                    @include('partials.message')

                    <a href="" class="btn btn-primary create"> <i class="fa fa-plus"></i> Create New</a>
                    <a href="" class="btn btn-default"> <i class="fa fa-file-excel-o"></i> Export</a>
                    <a href="" class="btn btn-default"> <i class="fa fa-file-excel-o"></i> Import</a>
                    <a href="" class="btn btn-danger truncate"> <i class="fa fa-trash"></i> Truncate</a>

                    <hr>

                            
                    <form action="{{ url()->current() }}" method="get" role="form">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3">Search for:</span>
                            <input type="text" class="form-control" name="query" value="{{ request('query') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </span>
                        </div>
                    </form>
    
                    <table class="table table-border">
                    	<thead>
                            <th><input type="checkbox"></th>
                    		<th>Name</th>
                    		<th>Email</th>
                    		<th>Status</th>
                            <th>Join Date</th>
                    		<th>Actions</th>
                    	</thead>
                    	@foreach ($subscribers as $subscriber)
                    	<tr class="{{ $subscriber->status === 'unsubscribed' ? 'text-muted' : '' }}">
                            <td><input type="checkbox" value="{{ $subscriber->id }}"></td>
                    		<td>{{ $subscriber->name }}</td>
                    		<td>{{ $subscriber->email }}</td>
                    		<td><span class="label label-{{ $labels[$subscriber->status] }}">{{ $subscriber->status }}</span></td>
                            <td>{{ $subscriber->created_at->format('d.m.Y H.i') }}</td>
                    		<td class="text-right">
                                <a href="" class="btn btn-default" title="Send newsletter"><i class="fa fa-envelope"></i></a>
                                <a href="" class="btn btn-default" title="Edit current subscriber"><i class="fa fa-pencil"></i></a>
                    			<a href="{{ route('admin.subscriber.delete', $subscriber->id) }}" class="btn btn-danger delete" title="Delete current subscriber"><i class="fa fa-trash"></i></a>
                    		</td>
                    	</tr>
                    	@endforeach
                    </table>

                    {{ $subscribers->appends([
                        'query' => request('query'),
                        'by' => request('by'),
                        'column' => request('column')
                    ])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="truncateModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Warning</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure wan to delete all items? This action can't be undone.</p>
                <p>Please insert your password before take this action.</p>
                <form action="">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="confirm-password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger"> <i class="fa fa-trash"></i> Truncate Subscribers</button>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Warning</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure wan to delete this item? This action can't be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger delete"> <i class="fa fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create New Subscriber</h4>
            </div>
            <form action="{{ route('admin.subscriber.create.post') }}" method="post" role="form" id="create-subscriber">
            {{ csrf_field() }}
            {{ method_field('post') }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary delete"> <i class="fa fa-save"></i> Create Subscriber</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(function(){
        // delete all data
        $('a.truncate').click(function(){
            $('#truncateModal').modal()
            $('#confirm-password').focus()
            return false
        })

        // delete single row
        $('a.delete').click(function(){
            $('#deleteModal').modal()
            $('button.delete').attr('data-url', $(this).attr('href'))
            return false
        })

        $('button.delete').click(function(){
            $(location).attr('href', $(this).attr('data-url'))
        })

        $('a.create').click(function(){
            $('#createModal').modal()
            return false
        })
    })
</script>
@endpush