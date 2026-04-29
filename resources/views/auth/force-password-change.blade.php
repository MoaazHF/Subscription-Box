@php($title = 'Change Password')

@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-[720px]">
        <div class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Security setup</p>
            <h1 class="mt-3 text-[2.25rem] font-bold tracking-[-0.04em] text-ink">Change your password</h1>
            <p class="mt-3 text-sm leading-7 text-ash">This account was created by admin. Set a new password to continue.</p>

            <form method="POST" action="{{ route('password.change.update') }}" class="mt-8 space-y-5">
                @csrf
                @method('PATCH')

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-ink">New password</label>
                    <input id="password" name="password" type="password" class="air-input" required>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-ink">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="air-input" required>
                </div>

                <button type="submit" class="air-button-primary w-full">Save password and continue</button>
            </form>
        </div>
    </section>
@endsection
