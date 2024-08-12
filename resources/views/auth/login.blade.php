@extends('app')

@section('content')

<div class="row">

    <form>

        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="email">
        </div>

        <div class="mb-3">
            <input type="password" name="password" type="password" class="form-control" placeholder="password">
        </div>
        

        <button class="btn btn-primary">login</button>

    </form>

    <div class="mt-3">
        <a href="/register">Sign up</a>
    </div>


</div>



@endsection


@section('scripts')

<script>

    let frm=document.querySelector('form');

    frm.addEventListener('submit',async(e)=>{

        e.preventDefault();

        let data=new FormData(frm);

        console.log(data);

        let obj={};

        data.forEach((v,i)=>{

            obj[i]=v;
        })

        console.log('this is the obj',obj);

        console.log('form submitted');

        let fe=await fetch('/hello',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(obj)});
        let re=await fe.json();

        console.log(re);
    })


</script>

@endsection