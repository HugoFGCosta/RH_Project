{{-- NAO ESTA PRONTO --}}


{{-- 
<form method="SHOW" action="{{ url('players') }}" enctype="multipart/form-data">

   @csrf
   <div class="form-group">
      <label for="name">Name</label>
      <input
            type="text"
            id="name"
            name="name"
            autocomplete="name"
            placeholder="Type your name"
            class="form-control
            @error('name') is-invalid @enderror"
            value="{{ $player->name }}"
            required
            aria-describedby="nameHelp"
            disabled>
      <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
      @error('name')
      <span class="invalid-feedback" role="alert">
      <strong>{{ $message }}</strong>
      </span>
      @enderror
      </div>
      <div class="form-group">
   <label for="name">Address</label>
      <input
         type="text"
         id="address"
         name="address"
         autocomplete="name"
         placeholder="Type your address"
         class="form-control
         @error('address') is-invalid @enderror"
         value="{{ $player->address->address}} "
         required
         aria-describedby="nameHelp" disabled>
      <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
      @error('name')
         <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
         </span>
      @enderror
   </div>
   
   <div class="form-group">
   <label for="name">City</label>
      <input
         type="text"
         id="city"
         name="city"
         autocomplete="name"
         placeholder="Type your city"
         class="form-control
         @error('city') is-invalid @enderror"
         value="{{ $player->address->city}} "
         required
         aria-describedby="nameHelp" disabled>
      <small id="nameHelp" class="form-text text-muted">We'll never share your data with anyone else.</small>
      @error('name')
         <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
         </span>
      @enderror
   </div>
      <div class="form-group">
         <label for="exampleFormControlTextarea1">Example textarea</label>
         <textarea class="form-control" name="description"  id="textArea" rows="5" disabled>{{ $player->description}}</textarea>
      </div>


      <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="retired" id="inlineRadio1"  @if ($player->retired)  checked @endif disabled>
        <label class="form-check-label" for="inlineRadio1">YES</label>
      </div>


      <div class="form-check form-check-inline">
         <input class="form-check-input" type="radio" name="retired" id="inlineRadio2" @if (!$player->retired)  checked @endif disabled>
         <label class="form-check-label" for="inlineRadio2">NO</label>
      </div>
      <br>
      <img class="w-100 img-responsive" src="{{ asset('storage/'.$player->image) }}" alt="" title=""></a>
        
     


   <a href="/players" type="button" class="mt-2 mb-5 btn btn-primary">Back</a>
</form> --}}
