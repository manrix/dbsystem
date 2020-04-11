@extends('layouts.app')

@section('content')
    @if (isset($new_version) && $new_version)
        <style type="text/css">
            .new-version-notification {
                margin-bottom: 0 !important;
                padding-top: .75rem;
                padding-bottom: .75rem;
                padding-left: 0;
                padding-right: 0;
            }
        </style>
        <div class="notification is-warning new-version-notification is-radiusless">
            <div class="container">
                <div class="level">
                    <div class="level-left has-text-weight-semibold">Version {{ $new_version }} available</div>
                    <div class="level-right has-text-right-desktop">
                        <a href="{{ route('upgrade') }}" class="button is-dark is-small is-outlined">Update Now</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="app"></div>
@endsection
