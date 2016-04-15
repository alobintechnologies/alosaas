@extends("layout")

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Accept Invitation</div>
            <div class="panel-body">
              <h1>You have been invited to join {{ $invitation->account->name }}</h1>
            </div>
        </div>
    </div>
</div>
@endsection
