@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="user-wrapper">
                <ul class="users">
                    @foreach($users as $row)
                    <li class="user" id="{{ $row->id }}">
                        @if($row->unread)
                        <span class="pending">{{ $row->unread }}</span>
                        @endif
                        <div class="media">
                            <div class="media-left">
                                <img src="{{ asset($row->avatar) }}" class="media-object">
                            </div>
                            <div class="media-body">
                                <p class="name">{{ $row->name }}</p>
                                <p class="email">{{ $row->email }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-8" id="message">
            
        </div>
    </div>
</div>
@endsection
