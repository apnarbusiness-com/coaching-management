@extends('layouts.admin')

@section('title', 'Teacher ID Card')

@section('styles')
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .print-container { margin: 0 !important; padding: 0 !important; box-shadow: none !important; }
            .id-card { break-inside: avoid; page-break-inside: avoid; box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }

        .id-card {
            width: 324px;
            height: 510px;
            background-color: white;
            position: relative;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .bg-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 12px 12px;
            opacity: 0.15;
        }

        .wave-top {
            position: absolute;
            top: -10px;
            left: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            clip-path: ellipse(80% 60% at 20% 0%);
            z-index: 1;
        }

        .wave-top-accent {
            position: absolute;
            top: -10px;
            left: 0;
            width: 100%;
            height: 130px;
            background: #f07e23;
            clip-path: ellipse(80% 60% at 15% 0%);
            z-index: 0;
        }

        .wave-bottom {
            position: absolute;
            bottom: -10px;
            right: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            clip-path: ellipse(80% 60% at 80% 100%);
            z-index: 1;
        }

        .wave-bottom-accent {
            position: absolute;
            bottom: -10px;
            right: 0;
            width: 100%;
            height: 130px;
            background: #f07e23;
            clip-path: ellipse(80% 60% at 85% 100%);
            z-index: 0;
        }

        .grid-dots-top {
            position: absolute;
            top: 10px;
            right: 10px;
            display: grid;
            grid-template-columns: repeat(6, 4px);
            gap: 4px;
            z-index: 2;
        }

        .grid-dots-bottom {
            position: absolute;
            bottom: 15px;
            left: 10px;
            display: grid;
            grid-template-columns: repeat(6, 4px);
            gap: 4px;
            z-index: 2;
        }

        .dot {
            width: 4px;
            height: 4px;
            background-color: #334155;
            border-radius: 50%;
        }

        .card-header {
            width: 100%;
            background: #2c2b56;
            padding: 12px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .card-header img {
            height: 32px;
            object-fit: contain;
        }
    </style>
@endsection

@section('content')
    <div>
        <header class="no-print max-w-4xl mx-auto mb-8 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Teacher ID Card</h1>
                <p class="text-sm text-slate-500">Your identification card</p>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-medium transition-all" onclick="window.print()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect height="8" width="12" x="6" y="14"></rect>
                    </svg>
                    Print Card
                </button>
            </div>
        </header>

        <main class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-center gap-12 print:flex-col print:gap-8">
            <!-- Front Side -->
            <section class="flex flex-col items-center gap-4">
                <span class="no-print text-sm font-semibold text-slate-400 uppercase tracking-widest">Front Side</span>
                <div class="id-card" id="card-front">
                    <div class="bg-pattern absolute inset-0 pointer-events-none"></div>
                    
                    <!-- Decoration Elements -->
                    <div class="wave-top-accent"></div>
                    <div class="wave-top"></div>
                    <div class="grid-dots-top">
                        @for($i = 0; $i < 24; $i++)
                            <div class="dot"></div>
                        @endfor
                    </div>

                    <!-- Logo Section -->
                    <div class="relative z-10 mt-6 mb-4">
                        <img alt="Logo" class="h-12 mx-auto" src="{{ asset('assets/images/logo_for_id.svg') }}">
                    </div>

                    <!-- Teacher Image -->
                    <div class="relative z-10">
                        <div class="w-36 h-36 rounded-full border-[5px] border-slate-800 overflow-hidden bg-slate-100 flex items-center justify-center">
                            @if($teacher && $teacher->profile_img)
                                <img src="{{ $teacher->profile_img->getUrl('preview') }}" alt="Teacher Portrait" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl font-bold text-slate-400">{{ $teacher ? substr($teacher->name, 0, 1) : 'T' }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Information -->
                    <div class="relative z-10 text-center mt-4 px-4">
                        <h2 class="text-2xl font-black text-slate-800 leading-tight">
                            {{ $teacher ? strtoupper($teacher->name) : 'TEACHER NAME' }}
                        </h2>
                        <div class="mt-3 space-y-1">
                            <p class="text-lg font-bold text-slate-700 tracking-wide">TEACHER</p>
                            <p class="text-base font-medium text-slate-600">{{ config('app.name', 'Coaching Center') }}</p>
                        </div>
                        <div class="mt-4 flex flex-col items-center gap-2">
                            <!-- Contact -->
                            <div class="flex items-center gap-2 text-slate-800">
                                <div class="w-6 h-6 bg-slate-800 rounded-full flex items-center justify-center text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <span class="text-base font-bold">{{ $teacher->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="wave-bottom-accent"></div>
                    <div class="wave-bottom"></div>
                    <div class="grid-dots-bottom">
                        @for($i = 0; $i < 24; $i++)
                            <div class="dot"></div>
                        @endfor
                    </div>
                </div>
            </section>

            <!-- Back Side -->
            <section class="flex flex-col items-center gap-4">
                <span class="no-print text-sm font-semibold text-slate-400 uppercase tracking-widest">Back Side</span>
                <div class="id-card" id="card-back">
                    <div class="bg-pattern absolute inset-0 pointer-events-none"></div>
                    
                    <!-- School Header -->
                    <div class="w-full bg-slate-800 p-4 text-center text-white relative z-10">
                        <h3 class="text-lg font-bold tracking-tight">{{ config('app.name', 'EXCELLENCY') }}</h3>
                        <p class="text-[8px] uppercase tracking-widest text-slate-300">Learn to Serve The Nation</p>
                    </div>

                    <!-- Identity Details -->
                    <div class="mt-6 px-5 text-center w-full z-10">
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 mb-4">
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Employee ID</p>
                            <p class="text-base font-mono font-bold text-slate-800">{{ $teacher->emloyee_code ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-3 text-left">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Address</p>
                                <p class="text-xs text-slate-700 leading-snug">{{ $teacher->address ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Email</p>
                                <p class="text-xs text-slate-700 font-bold">{{ $teacher->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="mt-4 z-10 flex flex-col items-center">
                        <div class="w-20 h-20 bg-white border-2 border-slate-100 p-1">
                            @if($teacher)
                            <img alt="Verification QR Code" class="w-full h-full" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('admin.teachers.show', $teacher->id)) }}" />
                            @else
                            <div class="w-full h-full bg-slate-200"></div>
                            @endif
                        </div>
                        <p class="text-[8px] mt-1 text-slate-400 uppercase tracking-tighter">Scan to verify</p>
                    </div>

                    <!-- Footer Signature -->
                    <div class="absolute bottom-12 w-full px-8 z-10">
                        <div class="border-t border-slate-300 pt-1 text-center">
                            <p class="text-[9px] font-bold text-slate-800 uppercase">Principal / Director</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 w-full h-2 bg-[#f07e23] z-10"></div>
                </div>
            </section>
        </main>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {});
    </script>
@endsection
