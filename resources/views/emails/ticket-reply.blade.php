@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">New reply to your ticket</h1>
    <p style="margin:0 0 16px;">Our support team has replied to your ticket <strong>{{ $ticket->reference }}</strong> — "{{ $ticket->subject }}".</p>
    <a href="{{ route('tickets.show', $ticket) }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">View &amp; reply</a>
@endsection
