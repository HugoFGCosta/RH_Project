<link rel="stylesheet" href="{{ asset('css/time-bank-balance.css') }}">

<div class="divGeral">

    <h1>Consultar Saldo</h1>

    <form class="teste" action="/time-bank-balance">
        @csrf
        <div class="row">

            <div class="divMonth">

                <label class="labelMonth" for="month">Selecione o Mês:</label>
                <select class="inputClass" name="month" id="month">
                    <option value="Todos">Todos</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>

            </div>

        </div>

        <div class="groupYear">
            <label for="year">Selecione o Ano:</label>
            <input class="inputClass" required type="number" name="year" id="year" min="0" max="5000"
                   @if($year != null )
                       value="{{ $year }}"
                @endif
            >
        </div>


        <input class="submitButton" type="submit" value="Filter">

        <div class="textsDiv">
            <h4>Mês: {{ $month }}</h4>
            <h4>Ano: {{ $year }}</h4>
            <p><span class="spanTimeOne">Saldo de Presenças</span>{{ $bankFormattedPresencas }}</p>
            <p><span class="spanTimeTwo">Saldo de Faltas</span>{{ $bankFormattedFaltas }}</p>
            <p><span class="spanTimeThree">Saldo Total</span>{{ $bankTotal }}</p>
        </div>

    </form>




</div>

