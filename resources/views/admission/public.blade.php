<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --brand: #0f766e;
            --brand-dark: #0b5f59;
            --accent: #f59e0b;
            --surface: #ffffff;
            --border: #e2e8f0;
            --bg: #eef6f3;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background: radial-gradient(circle at top right, #e5f6ff 0%, #eef6f3 45%, #f8fafc 100%);
            color: var(--ink);
        }

        .admission-wrap {
            padding: 32px 12px 48px;
        }

        .admission-card {
            background: var(--surface);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .admission-hero {
            background: linear-gradient(135deg, #0f766e, #0ea5e9);
            color: #fff;
            padding: 32px 28px;
        }

        .admission-hero h1 {
            font-weight: 800;
            letter-spacing: 0.4px;
            margin-bottom: 8px;
        }

        .admission-hero p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        .admission-section {
            padding: 28px;
            border-bottom: 1px solid var(--border);
        }

        .admission-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 18px;
            color: var(--brand-dark);
        }

        .badge-tag {
            background: rgba(245, 158, 11, 0.15);
            color: #b45309;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        .form-control,
        .custom-select {
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 10px 12px;
        }

        .form-control:focus,
        .custom-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.15rem rgba(15, 118, 110, 0.15);
        }

        .btn-submit {
            background: var(--brand);
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 12px;
        }

        .btn-submit:hover {
            background: var(--brand-dark);
        }

        .terms-card {
            background: #f8fafc;
            border-radius: 14px;
            border: 1px dashed #cbd5f5;
            padding: 16px;
        }

        @media (max-width: 768px) {
            .admission-hero {
                padding: 24px 20px;
            }

            .admission-section {
                padding: 22px 18px;
            }
        }
    </style>
</head>

<body>
    <div class="admission-wrap">
        <div class="admission-card mx-auto" style="max-width: 980px;">
            <div class="admission-hero">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                    <div>
                        <h1>Admission Form</h1>
                        <p class="mb-2">Roy Niketan, College Gate, Main Road, Agailjhara, Barishal</p>
                        <p>Email: excellencybn@gmail.com | Mobile: 01683-546 013</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <span class="badge-tag">Public URL • No Login Required</span>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="admission-section">
                    <div class="alert alert-danger">
                        <strong>Please fix the following:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admission.public.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="admission-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="section-title">Admission Info</h4>
                        <span class="text-muted">Fill carefully</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Admission Date</label>
                            <input type="date" name="admission_date" class="form-control"
                                value="{{ old('admission_date', now()->toDateString()) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Admission ID No (optional)</label>
                            <input type="text" name="admission_id_no" class="form-control"
                                value="{{ old('admission_id_no') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Student Photo (optional)</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="admission-section">
                    <h4 class="section-title">Student Information</h4>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required
                                value="{{ old('first_name') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name (optional)</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Gender</label>
                            <select name="gender" class="custom-select" required>
                                <option value="">Select</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="others" {{ old('gender') === 'others' ? 'selected' : '' }}>Others
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required
                                value="{{ old('dob') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Blood Group</label>
                            <select name="student_blood_group" class="custom-select">
                                <option value="">Select</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                                    <option value="{{ $group }}"
                                        {{ old('student_blood_group') === $group ? 'selected' : '' }}>
                                        {{ $group }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Mobile No</label>
                            <input type="text" name="contact_number" class="form-control" required
                                value="{{ old('contact_number') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email (optional)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Birth Registration No (optional)</label>
                            <input type="text" name="student_birth_no" class="form-control"
                                value="{{ old('student_birth_no') }}">
                        </div>
                    </div>
                </div>

                <div class="admission-section">
                    <h4 class="section-title">Family & Guardian</h4>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Father's Name</label>
                            <input type="text" name="fathers_name" class="form-control"
                                value="{{ old('fathers_name') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mother's Name</label>
                            <input type="text" name="mothers_name" class="form-control"
                                value="{{ old('mothers_name') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Guardian Name (if different)</label>
                            <input type="text" name="guardian_name" class="form-control"
                                value="{{ old('guardian_name') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Guardian Relation</label>
                            <input type="text" name="guardian_relation" class="form-control"
                                value="{{ old('guardian_relation') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Guardian Mobile No</label>
                            <input type="text" name="guardian_contact_number" class="form-control" required
                                value="{{ old('guardian_contact_number') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Guardian Email (optional)</label>
                            <input type="email" name="guardian_email" class="form-control"
                                value="{{ old('guardian_email') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                    </div>
                </div>

                <div class="admission-section">
                    <h4 class="section-title">Academic Details</h4>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>School Name</label>
                            <input type="text" name="school_name" class="form-control"
                                value="{{ old('school_name') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Class</label>
                            <input type="text" name="class_name" class="form-control"
                                value="{{ old('class_name') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Class Roll</label>
                            <input type="text" name="class_roll" class="form-control"
                                value="{{ old('class_roll') }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Batch Name</label>
                            <input type="text" name="batch_name" class="form-control"
                                value="{{ old('batch_name') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Village</label>
                            <input type="text" name="village" class="form-control" value="{{ old('village') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Post Office</label>
                            <input type="text" name="post_office" class="form-control"
                                value="{{ old('post_office') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Subjects</label>
                        <div class="d-flex flex-wrap">
                            @foreach (['Bangla', 'English', 'Math', 'Science', 'ICT'] as $subject)
                                <div class="custom-control custom-checkbox mr-4 mb-2">
                                    <input type="checkbox" class="custom-control-input"
                                        id="subject-{{ $subject }}" name="subjects[]"
                                        value="{{ $subject }}"
                                        {{ in_array($subject, old('subjects', [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="subject-{{ $subject }}">
                                        {{ $subject }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admission-section">
                    <div class="terms-card mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="terms" name="terms"
                                required>
                            <label class="custom-control-label" for="terms">
                                I accept the terms & conditions mentioned by the authority.
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">Your information will be reviewed before the student
                            account is created.</small>
                    </div>

                    <button type="submit" class="btn btn-submit text-white">
                        Submit Admission Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
