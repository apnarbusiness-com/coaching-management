@extends('layouts.app')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background: linear-gradient(135deg, #f8fafc, #e0f2fe);
        }

        .thanks-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            padding: 32px;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="thanks-card mx-auto" style="max-width: 680px;">
            <h2 class="mb-3">Thank you! Your admission form has been submitted.</h2>
            <p class="text-muted mb-4">
                Application ID: <strong>#{{ $application->id }}</strong>
            </p>
            <p class="mb-0">
                Our team will review your information. If anything is missing, we will contact you using the provided
                phone number.
            </p>
        </div>
    </div>
@endsection
