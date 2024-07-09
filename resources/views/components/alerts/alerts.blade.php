<!-- resources/views/components/alerts.blade.php -->
@if (session('success') || session('error'))
    <div id="modal-container" class="modal-container">
        <div class="modal">
            <span class="close-btn" id="close-btn">&times;</span>
            <div class="modal-content">
                @if (session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert error">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
