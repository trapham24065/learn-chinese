@extends('layouts.app')

@section('title', 'Khoa Hoc Cua Toi | Learn Chinese')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tai khoan hoc vien</p>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Khoa Hoc Cua Toi</h1>
        </div>
        @if(setting_bool('feature_courses', true))
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">Dang ky them</a>
        @endif
    </div>

    @if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-3.5">
        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-2xl bg-red-50 border border-red-200 px-5 py-3.5">
        <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    @if($registrations->isEmpty())
    <div class="rounded-3xl border border-dashed border-slate-300 bg-white/60 p-12 text-center space-y-4">
        <p class="text-base font-bold text-slate-700">Chua co don dang ky nao</p>
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#991b1b] px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-800 transition">Kham pha khoa hoc</a>
        <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-5 text-left">
            <p class="text-xs text-slate-500 mb-4">Email: {{ auth()->user()->email }}</p>
            <form method="POST" action="#" id="claim-form" class="flex gap-2">
                @csrf
                <input type="text" name="code" placeholder="REG..." maxlength="12" required class="flex-1 rounded-xl border px-3 py-2 text-xs">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white">Lien ket</button>
            </form>
            <script>document.getElementById('claim-form').addEventListener('submit',function(e){e.preventDefault();var c=this.querySelector('[name=code]').value.trim().toUpperCase();if(c.length<8)return;this.action='/courses/claim/'+c;this.submit();});</script>
        </div>
    </div>

    @else
    <div class="space-y-4">
        @foreach($registrations as $reg)
        @php
            $statusColors = ['pending'=>'bg-amber-50 border-amber-200 text-amber-800','contacted'=>'bg-blue-50 border-blue-200 text-blue-800','paid'=>'bg-emerald-50 border-emerald-200 text-emerald-800','enrolled'=>'bg-green-50 border-green-200 text-green-800','cancelled'=>'bg-red-50 border-red-200 text-red-800'];
            $badgeClass = $statusColors[$reg->status] ?? 'bg-slate-50 border-slate-200 text-slate-700';
        @endphp
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-base font-black text-slate-900">{{ $reg->course->title }}</p>
                    <p class="text-sm font-semibold text-slate-700 mt-1">{{ $reg->full_name }}</p>
                    @if($reg->courseClass)<p class="text-xs text-slate-500">{{ $reg->courseClass->name }}</p>
                    @elseif($reg->preferred_schedule)<p class="text-xs text-slate-500">{{ $reg->preferred_schedule }}</p>@endif
                </div>
                <span class="shrink-0 rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $badgeClass }}">{{ $reg->status_label }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div><p class="text-slate-400">Ma don</p><p class="font-black font-mono text-[#991b1b]">{{ $reg->registration_code }}</p></div>
                <div><p class="text-slate-400">Thanh toan</p><p>{{ $reg->payment_status_label }}</p></div>
                <div><p class="text-slate-400">Hoc phi</p><p class="font-black">{{ $reg->formatted_payment_amount }}</p></div>
                <div><p class="text-slate-400">Noi dung CK</p><p class="font-black font-mono">{{ $reg->payment_reference }}</p></div>
            </div>
            <div class="flex flex-wrap gap-2 pt-1 border-t border-slate-100">
                <a href="{{ route('courses.success', $reg->registration_code) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700">Xem QR chuyen khoan</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection