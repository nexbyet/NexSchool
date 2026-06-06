@extends('install.master')
@section('content')
<div class="cd p-6">
    <h2 class="text-base font-semibold text-white">License Activation</h2>
    <p class="text-gray-400 text-xs mt-1 mb-5">Enter your license key (optional — can skip).</p>
    <div class="ig mb-4"><label>License Key</label><textarea id="licenseKey" rows="3" placeholder="Paste your license key here...">{{ session('install.license_key') }}</textarea></div>
    <div id="licenseResult" style="display:none" class="mb-4"></div>
    <div class="flex gap-3">
        <button type="button" id="activateBtn" class="btn btn-p flex-1"><i class="lni lni-shield-2"></i> Activate</button>
        <a href="{{ route('install.admin') }}" id="skipBtn" class="btn btn-g flex-1">Skip <i class="lni lni-arrow-right"></i></a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('activateBtn').addEventListener('click', async function(){
    const btn=this, r=document.getElementById('licenseResult'), k=document.getElementById('licenseKey').value.trim();
    if(!k){r.style.display='block';r.innerHTML='<div class="alert-e"><i class="lni lni-warning"></i> Enter a license key or skip.</div>';return}
    btn.disabled=!0; btn.innerHTML='<span class="sp"></span> Verifying...';
    const t=document.querySelector('meta[name=csrf-token]').content;
    try{
        const res=await fetch('{{ route("install.license.activate") }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':t},body:JSON.stringify({_token,license_key:k})});
        const j=await res.json();
        r.style.display='block';
        if(j.success){r.innerHTML='<div class="alert-s"><i class="lni lni-checkmark-circle"></i> '+j.message+'</div>';setTimeout(()=>window.location.href='{{ route("install.admin") }}',600)}
        else{r.innerHTML='<div class="alert-e"><i class="lni lni-warning"></i> '+j.message+'</div>';btn.disabled=!1;btn.innerHTML='<i class="lni lni-shield-2"></i> Activate'}
    }catch(e){r.style.display='block';r.innerHTML='<div class="alert-e"><i class="lni lni-warning"></i> Server unreachable.</div>';btn.disabled=!1;btn.innerHTML='<i class="lni lni-shield-2"></i> Activate'}
});
</script>
@endpush
