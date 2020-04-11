@extends('layouts.app')

@section('head_scripts')@endsection

@section('content')
    <div class="hero is-fullheight">
        <div class="hero-body">
            <div class="container is-fluid">
                <div class="columns is-centered is-vcentered">
                    <div class="column is-4-desktop is-3-fullhd">
                        <div class="content has-text-centered">
                            <p>
                                <img alt="Logo" src="img/logo-square.svg" style="width: 75px;">
                            </p>
                        </div>
                        <div class="card">
                            <div class="card-content">
                                <h1 class="title is-4 has-text-centered">Application Upgrade</h1>
                                <hr>
                                @if (session('message'))
                                    <article class="message is-success">
                                        <div class="message-body">
                                            {{ session('message') }}
                                        </div>
                                    </article>
                                    <div class="field">
                                        <div class="control">
                                            <a href="{{ route('backend') }}" class="button is-block is-link is-fullwidth is-uppercase">Home</a>
                                        </div>
                                    </div>
                                @else
                                    <div class="level">
                                        <div class="level-left">
                                            <span class="has-text-info">Current version:</span>
                                        </div>
                                        <div class="level-right">
                                            <code>{{ config('app.version') }}</code>
                                        </div>
                                    </div>
                                    @if ($version)
                                        <div class="level">
                                            <div class="level-left">
                                                <span class="has-text-success has-text-weight-semibold is-size-5">Available version:</span>
                                            </div>
                                            <div class="level-right">
                                                <code>{{ $version }}</code>
                                            </div>
                                        </div>
                                        <hr>
                                        <form method="POST" action="{{ route('upgrade') }}">
                                            {{ csrf_field() }}
                                            <div class="field">
                                                <div class="control">
                                                    <button type="submit" class="button is-block is-link is-fullwidth is-uppercase">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <article class="message is-primary">
                                            <div class="message-body">
                                                There isn't any new version available.
                                            </div>
                                        </article>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')@endsection
