<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted — Excellency</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --brand: #0f766e;
            --brand-dark: #0b5f59;
            --surface: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background: radial-gradient(circle at top right, #e5f6ff 0%, #eef6f3 45%, #f8fafc 100%);
            color: var(--ink);
            min-height: 100vh;
        }

        .page-wrap {
            padding: 32px 12px 48px;
        }

        .main-card {
            background: var(--surface);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            max-width: 780px;
            margin: 0 auto;
            overflow: hidden;
        }

        .hero {
            background: linear-gradient(135deg, #0f766e, #0ea5e9);
            color: #fff;
            padding: 28px 32px;
            text-align: center;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .hero p {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .body-section {
            padding: 32px;
        }

        .success-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            color: #15803d;
            font-weight: 700;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 40px;
            border: 1px solid #bbf7d0;
            margin-bottom: 24px;
        }

        .success-badge svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .student-profile {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .student-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--brand);
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .student-avatar-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 3px dashed var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #94a3b8;
        }

        .student-avatar-placeholder svg {
            width: 36px;
            height: 36px;
        }

        .student-name {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .student-ref {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-group:last-child {
            margin-bottom: 0;
        }

        .info-group-title {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--brand-dark);
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e2e8f0;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--muted);
            font-weight: 500;
            flex-shrink: 0;
            min-width: 100px;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
            color: var(--ink);
        }

        .status-note {
            background: #fefce8;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.9rem;
            color: #92400e;
            margin-top: 24px;
        }

        .status-note strong {
            color: #78350f;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .btn-print {
            background: var(--brand);
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: background 0.15s;
        }

        .btn-print:hover {
            background: var(--brand-dark);
            color: #fff;
            text-decoration: none;
        }

        .btn-outline {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-outline:hover {
            border-color: var(--brand);
            color: var(--brand);
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .body-section {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .student-profile {
                flex-direction: column;
                text-align: center;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 2px;
            }

            .info-value {
                text-align: left;
            }
        }

        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-wrap {
                padding: 0;
            }

            .main-card {
                box-shadow: none;
                border: none;
                border-radius: 0;
                max-width: 100%;
            }

            .hero {
                background: #0f766e !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 20px 24px;
            }

            .hero h1 {
                font-size: 1.2rem;
            }

            .actions,
            .no-print {
                display: none !important;
            }

            .body-section {
                padding: 20px 24px;
            }

            .student-avatar {
                border-width: 2px;
            }

            .success-badge {
                background: #f0fdf4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border-color: #bbf7d0 !important;
            }

            .status-note {
                background: #fefce8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border-color: #fde68a !important;
            }

            .info-item {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="main-card">
            <div class="hero">
                <h1>Excellency Coaching Center</h1>
                <p>Roy Niketan, College Gate, Main Road, Agailjhara, Barishal</p>
            </div>

            <div class="body-section">
                <div class="success-badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Application Submitted Successfully
                </div>

                @php
                    $details = $student->studentDetails;
                    $ref = $details ? json_decode($details->reference, true) : [];
                @endphp

                <div class="student-profile">
                    @if ($student->image && $student->image->url)
                        <img src="{{ $student->image->url }}" alt="{{ $student->first_name }}" class="student-avatar">
                    @else
                        <div class="student-avatar-placeholder">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div class="student-ref">Application Ref: #{{ $student->id }} &middot; {{ ucfirst($student->status) }}</div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-group-title">Contact Information</div>
                        <div class="info-item">
                            <span class="info-label">Mobile</span>
                            <span class="info-value">{{ $student->contact_number }}</span>
                        </div>
                        @if ($student->email)
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $student->email }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Gender</span>
                            <span class="info-value">{{ ucfirst($student->gender) }}</span>
                        </div>
                        @if ($ref['class_roll'] ?? null)
                            <div class="info-item">
                                <span class="info-label">Class Roll</span>
                                <span class="info-value">{{ $ref['class_roll'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="info-group">
                        <div class="info-group-title">Academic Details</div>
                        @if ($ref['school_name'] ?? null)
                            <div class="info-item">
                                <span class="info-label">School</span>
                                <span class="info-value">{{ $ref['school_name'] }}</span>
                            </div>
                        @endif
                        @if ($ref['class_name'] ?? null)
                            <div class="info-item">
                                <span class="info-label">Class</span>
                                <span class="info-value">{{ $ref['class_name'] }}</span>
                            </div>
                        @endif
                        @if ($ref['batch_name'] ?? null)
                            <div class="info-item">
                                <span class="info-label">Batch</span>
                                <span class="info-value">{{ $ref['batch_name'] }}</span>
                            </div>
                        @endif
                        @if ($ref['subjects'] ?? null)
                            <div class="info-item">
                                <span class="info-label">Subjects</span>
                                <span class="info-value">{{ is_array($ref['subjects']) ? implode(', ', $ref['subjects']) : $ref['subjects'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($details && ($details->fathers_name || $details->mothers_name || $details->guardian_name))
                    <div class="info-group" style="margin-top:20px;">
                        <div class="info-group-title">Guardian Information</div>
                        <div class="info-grid">
                            @if ($details->fathers_name)
                                <div class="info-item">
                                    <span class="info-label">Father</span>
                                    <span class="info-value">{{ $details->fathers_name }}</span>
                                </div>
                            @endif
                            @if ($details->mothers_name)
                                <div class="info-item">
                                    <span class="info-label">Mother</span>
                                    <span class="info-value">{{ $details->mothers_name }}</span>
                                </div>
                            @endif
                            @if ($details->guardian_name)
                                <div class="info-item">
                                    <span class="info-label">Guardian</span>
                                    <span class="info-value">{{ $details->guardian_name }} ({{ $details->guardian_relation ?? '—' }})</span>
                                </div>
                            @endif
                            @if ($details->guardian_contact_number)
                                <div class="info-item">
                                    <span class="info-label">Guardian Mobile</span>
                                    <span class="info-value">{{ $details->guardian_contact_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="status-note">
                    <strong>⏳ Pending Review.</strong> Our team will verify your information.
                    We will contact you at <strong>{{ $student->contact_number }}</strong> if anything is needed.
                </div>

                <div class="actions">
                    <button onclick="window.print()" class="btn-print">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print / Save as PDF
                    </button>
                    <a href="{{ url('/') }}" class="btn-outline">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>