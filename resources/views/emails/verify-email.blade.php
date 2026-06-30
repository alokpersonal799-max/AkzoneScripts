@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">Verify your email</h1>
    <p style="margin:0 0 16px;">Hi {{ $user->name }}, please confirm your email address to activate your account.</p>
    <a href="{{ $verifyUrl }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">Verify email</a>
    <p style="margin:16px 0 0;color:#94a3b8;font-size:12px;">This link expires in 48 hours. If you didn't create an account, ignore this email.</p>
@endsection
