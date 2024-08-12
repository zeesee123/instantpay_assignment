@extends('app')

@section('content')

<div class="row">

    <form>

        <div class="mb-3">
            <input type="email" name="email" placeholder="email" class="form-control">
        </div>

        <div class="mb-3">
            <input type="password" name="password" placeholder="password" class="form-control">
        </div>

        <div class="mb-3">
           <input type="password" name="password_confirmation" placeholder="confirm password" class="form-control">
        </div>


        <button class="btn btn-primary">sign up</button>


    </form>

    <div class="mt-3">
        <a href="/">login</a>
    </div>
    



</div>

@endsection

@section('scripts')

<script>

    let frm=document.querySelector('form');

    frm.addEventListener('submit',(e)=>{

        e.preventDefault();

        console.log('form submitted');
    })


</script>

@endsection