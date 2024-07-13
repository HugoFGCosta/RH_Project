<link rel="stylesheet" href="{{ asset('css/time-bank-balance.css') }}">

<div class="divGeral">
    <h1 class="titleBalance">Consultar Saldo</h1>
    <form class="teste" action="/time-bank-balance">
        @csrf
        <div class="row">
            <div class="groupYear">
                <label class="labelMonth" for="month">Selecione o Mês:</label>
                <select class="inputClass" name="month" id="month">
                    <option value="Todos" @if($month === null || $month == "Todos" || $month == "0") selected @endif>Todos</option>
                    <option value="1" @if($month == "Janeiro") selected @endif>Janeiro</option>
                    <option value="2" @if($month == "Fevereiro") selected @endif>Fevereiro</option>
                    <option value="3" @if($month == "Março") selected @endif>Março</option>
                    <option value="4" @if($month == "Abril") selected @endif>Abril</option>
                    <option value="5" @if($month == "Maio") selected @endif>Maio</option>
                    <option value="6" @if($month == "Junho") selected @endif>Junho</option>
                    <option value="7" @if($month == "Julho") selected @endif>Julho</option>
                    <option value="8" @if($month == "Agosto") selected @endif>Agosto</option>
                    <option value="9" @if($month == "Setembro") selected @endif>Setembro</option>
                    <option value="10" @if($month == "Outobro") selected @endif>Outubro</option>
                    <option value="11" @if($month == "Novembro") selected @endif>Novembro</option>
                    <option value="12" @if($month == "Dezembro") selected @endif>Dezembro</option>
                </select>
            </div>
        </div>

        <div class="groupYear">
            <label for="year">Selecione o Ano:</label>
            <input class="inputClass" required type="number" name="year" id="year" min="0" max="5000"
                   @if($year != null) value="{{ $year }}" @endif>
        </div>

        <input class="submitButton" type="submit" value="Filtro">

        <div class="textsDiv">
            <h4 class="marginAjust">Mês: {{ $month }}</h4>
            <h4 class="marginAjust">Ano: {{ $year }}</h4>
            <p><span class="spanTimeOne">Saldo de Presenças</span><span id="balancePresence">{{ $bankFormattedPresencas }}</span></p>
            <p><span class="spanTimeTwo">Saldo de Faltas</span><span id="balanceAbsence">{{ $bankFormattedFaltas }}</span></p>
            <p><span class="spanTimeThree">Saldo Total</span><span id="balanceTotal">{{ $bankTotal }}</span></p>
        </div>
    </form>
</div>


<script src="{{asset('js/time-bank-balance.js')}}"></script>
