@extends('layouts.app')

@section('title', 'Login')

@section('head_scripts')@endsection

@section('content')
    <div class="hero is-fullheight is-light">
        <div class="hero-body">
            <div class="container is-fluid">
                <div class="columns is-centered is-vcentered">
                    <div class="column is-4-desktop is-3-fullhd">
                        <div class="content has-text-centered">
                            <p>
                                <img alt="Logo" src="img/logo-square.svg" style="width: 75px;">
                            </p>
                            <h1 class="title is-5 has-text-link">{{ config('app.name') }}</h1>
                        </div>
                        <div class="card" style="margin-bottom: 1rem;">
                            <div class="card-content">
                                @if (!$errors->isEmpty())
                                    <article class="message is-danger is-small">
                                        <div class="message-body">
                                            {{ $errors->first() }}
                                        </div>
                                    </article>
                                @endif
                                <form method="POST" action="{{ route('login') }}" style="margin: 0">
                                    {{ csrf_field() }}
                                    <div class="field">
                                        <label class="label" for="email"></label>
                                        <div class="control has-icons-left">
                                            <input id="email" class="input is-medium"
                                                   type="email" name="email" value="{{ old('email') }}"
                                                   placeholder="Email address" required>
                                            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="label" for="password"></label>
                                        <div class="control has-icons-left">
                                            <input id="password" class="input is-medium"
                                                   name="password" type="password" placeholder="Password" required>
                                            <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <div class="control has-text-centered">
                                            <label class="b-checkbox checkbox">
                                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span class="check"></span>
                                                <span class="control-label">Remember me</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <div class="control">
                                            <button type="submit"
                                                    class="button is-block is-link is-fullwidth is-uppercase">Login
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <p class="has-text-centered has-text-grey">&copy; {{ config('app.name') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')@endsection
