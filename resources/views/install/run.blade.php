@extends('install.master')
@section('content')
<div class="cd p-6 text-center">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/15 mb-5">
        <i class="lni lni-cog text-2xl text-white animate-spin"></i>
    </div>
    <h2 class="text-base font-semibold text-white">Installing NexSchool...</h2>
    <p class="text-gray-400 text-xs mt-1 mb-6">Please wait while we set up everything.</p>

    <div id="progressList" class="space-y-2.5 text-left max-w-xs mx-auto">
        @foreach(['Preparing environment...','Generating app key...','Running migrations...','Creating admin...','Saving settings...','Finalizing...'] as $i => $label)
        <div class="flex items-center gap-2.5 text-sm text-gray-500" data-step="{{ $i }}" @if($i>0)style="display:none"@endif>
            <span class="sp"></span> <span>{{ $label }}</span>
        </div>
        @endforeach
    </div>

    <div id="errorBox" style="display:none" class="alert-e text-left text-sm mt-5"></div>
    <div id="retryBtn" style="display:none" class="mt-3"><button onclick="startInstall()" class="btn btn-g w-full">Retry</button></div>
</div>
@endsection

@push('scripts')
<script>
async function startInstall(){
    const steps=document.querySelectorAll('[data-step]');
    steps.forEach((el,i)=>{el.style.display='flex';el.style.opacity=i>0?'.3':'1';el.className='flex items-center gap-2.5 text-sm '+(i==0?'text-white':'text-gray-500')});
    document.getElementById('errorBox').style.display='none';
    document.getElementById('retryBtn').style.display='none';

    function markDone(step){
        steps.forEach((el,i)=>{
            if(i<step){el.innerHTML='<i class="lni lni-checkmark-circle text-emerald-400"></i> '+el.querySelector('span').textContent;el.style.opacity='1';el.className='flex items-center gap-2.5 text-sm text-emerald-400'}
            else if(i==step){el.style.display='flex';el.style.opacity='1';el.className='flex items-center gap-2.5 text-sm text-white'}
            else{el.style.display='flex';el.style.opacity='.3'}
        });
    }

    markDone(0);
    const t=document.querySelector('meta[name=csrf-token]').content;
    try{
        const res=await fetch('{{ route("install.process") }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':t},body:JSON.stringify({_token:t})});
        const j=await res.json();
        if(j.success){markDone(5);setTimeout(()=>window.location.href='{{ route("install.complete") }}',800)}
        else{document.getElementById('errorBox').style.display='block';document.getElementById('errorBox').innerHTML=j.message||'Installation failed.';document.getElementById('retryBtn').style.display='block'}
    }catch(e){document.getElementById('errorBox').style.display='block';document.getElementById('errorBox').innerHTML='Server unreachable.';document.getElementById('retryBtn').style.display='block'}
}
startInstall();
</script>
@endpush
