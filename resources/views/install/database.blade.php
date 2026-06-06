@extends('install.master')
@section('content')
<div class="cd p-6">
    <h2 class="text-base font-semibold text-white">Database Configuration</h2>
    <p class="text-gray-400 text-xs mt-1 mb-5">Enter your MySQL database credentials.</p>

    <div class="space-y-3.5 mb-5">
        <div class="ig"><label>Host</label><input type="text" id="host" value="{{ $db['host'] ?? session('install.db.host', '127.0.0.1') }}"></div>
        <div class="ig"><label>Port</label><input type="text" id="port" value="{{ $db['port'] ?? session('install.db.port', '3306') }}"></div>
        <div class="ig"><label>Database Name</label><input type="text" id="name" value="{{ $db['name'] ?? session('install.db.name', 'nexschool') }}"></div>
        <div class="ig"><label>Username</label><input type="text" id="user" value="{{ $db['user'] ?? session('install.db.user', 'root') }}"></div>
        <div class="ig"><label>Password</label><input type="password" id="pass" value="{{ $db['pass'] ?? session('install.db.pass', '') }}"></div>
    </div>

    <div id="dbResult" style="display:none" class="mb-4"></div>

    <div class="flex gap-3">
        <button type="button" id="testBtn" class="btn btn-g flex-1"><i class="lni lni-connection"></i> Test</button>
        <a href="{{ route('install.license') }}" id="continueBtn" class="btn btn-p flex-1" style="pointer-events:none;opacity:.4">Continue <i class="lni lni-arrow-right"></i></a>
    </div>
</div>
@if($db)
<script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('testBtn').click()});</script>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('testBtn').addEventListener('click', async function(){
    const btn=this, r=document.getElementById('dbResult'), c=document.getElementById('continueBtn');
    btn.disabled=!0; btn.innerHTML='<span class="sp"></span> Testing...';
    const t=document.querySelector('meta[name=csrf-token]').content;
    const d=JSON.stringify({_token:t,host:document.getElementById('host').value,port:document.getElementById('port').value,name:document.getElementById('name').value,user:document.getElementById('user').value,pass:document.getElementById('pass').value});
    try{
        const res=await fetch('{{ route("install.database.test") }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':t},body:d});
        const j=await res.json();
        r.style.display='block';
        if(j.success){r.innerHTML='<div class="alert-s"><i class="lni lni-checkmark-circle"></i> '+j.message+'</div>';c.style.pointerEvents='auto';c.style.opacity='1'}
        else{r.innerHTML='<div class="alert-e"><i class="lni lni-warning"></i> '+j.message+'</div>';c.style.pointerEvents='none';c.style.opacity='.4'}
    }catch(e){r.style.display='block';r.innerHTML='<div class="alert-e"><i class="lni lni-warning"></i> Server unreachable.</div>';c.style.pointerEvents='none';c.style.opacity='.4'}
    finally{btn.disabled=!1;btn.innerHTML='<i class="lni lni-connection"></i> Test'}
});
</script>
@endpush
