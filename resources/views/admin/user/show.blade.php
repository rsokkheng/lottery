{{-- Updated Blade Template --}}
<x-admin>
    @section('title', 'User Betting Limits Setting')
    
    {{-- Keep your existing styles --}}
    <style>
        .form-section {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .bet-limits-table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .bet-limits-table th {
            background: #007bff;
            color: white;
            font-weight: 500;
            padding: 12px;
            text-align: center;
        }
        
        .bet-limits-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .bet-limits-table .game-name {
            font-weight: 600;
            color: #495057;
            text-align: center;
        }
        
        .limit-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            color: black;
        }
        
        .limit-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
            outline: 0;
        }
        
        .btn-save {
            background: #007bff;
            border: none;
            padding: 10px 30px;
            font-weight: 500;
            color: white;
        }
        
        .btn-save:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-10">
                    <h4>User: {{ $user->name ?? 'N/A' }} - Betting Limits</h4>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="package_type" id="defaultPackage" 
                                    value="default" onchange="togglePackageInputs()">
                                <label class="form-check-label" for="defaultPackage">
                                    <strong>Default Package</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="package_type" id="customPackage" 
                                    value="custom" checked onchange="togglePackageInputs()">
                                <label class="form-check-label" for="customPackage">
                                    <strong>Custom Package</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.user.setting', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="package_type" id="selectedPackageType" value="custom">
                
                {{-- Betting Limits Section --}}
                <div class="form-section" id="bettingLimitsSection">
                    <div class="table-responsive">
                        <table class="table bet-limits-table">
                            <thead>
                                <tr>
                                    <th>Game Type</th>
                                    <th>Minimum Bet</th>
                                    <th>Maximum Bet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formatted as $digitKey => $setting)
                                <tr>
                                    <td class="game-name">{{ $setting['label'] }}</td>
                                    <td>
                                        <input type="number" 
                                               class="limit-input" 
                                               name="min_{{ strtolower(str_replace(['2D', '3D', '4D', 'RP2', 'RP3'], ['digit_2', 'digit_3', 'digit_4', 'digit_rp2', 'digit_rp3'], $digitKey)) }}" 
                                               step="0.1" 
                                               min="0"
                                               value="{{ old('min_' . strtolower(str_replace(['2D', '3D', '4D', 'RP2', 'RP3'], ['digit_2', 'digit_3', 'digit_4', 'digit_rp2', 'digit_rp3'], $digitKey)), $setting['min_bet']) }}"
                                               placeholder="Enter minimum bet">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="limit-input" 
                                               name="max_{{ strtolower(str_replace(['2D', '3D', '4D', 'RP2', 'RP3'], ['digit_2', 'digit_3', 'digit_4', 'digit_rp2', 'digit_rp3'], $digitKey)) }}" 
                                               step="0.1" 
                                               min="0"
                                               value="{{ old('max_' . strtolower(str_replace(['2D', '3D', '4D', 'RP2', 'RP3'], ['digit_2', 'digit_3', 'digit_4', 'digit_rp2', 'digit_rp3'], $digitKey)), $setting['max_bet']) }}"
                                               placeholder="Enter maximum bet">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="row">
                    <div class="col-12">
                        <div class="float-right">
                            <button class="btn btn-secondary mr-2" type="button" onclick="resetForm()">Reset</button>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePackageInputs() {
            const packageType = document.querySelector('input[name="package_type"]:checked').value;
            const bettingSection = document.getElementById('bettingLimitsSection');
            const limitInputs = document.querySelectorAll('.limit-input');
            const selectedPackageType = document.getElementById('selectedPackageType');
            
            selectedPackageType.value = packageType;
            
            if (packageType === 'default') {
                bettingSection.style.opacity = '0.6';
                limitInputs.forEach(input => {
                    input.disabled = true;
                });
                
                // Set default values
                setDefaultValues();
            } else {
                bettingSection.style.opacity = '1';
                limitInputs.forEach(input => {
                    input.disabled = false;
                });
            }
        }
        
   
        
        function resetForm() {
            if (confirm('Are you sure you want to reset all values?')) {
                document.querySelectorAll('.limit-input').forEach(input => {
                    input.value = '';
                });
            }
        }
        
        // Form validation
        function validateForm() {
            const inputs = document.querySelectorAll('.limit-input');
            let isValid = true;
            
            // Check if max is greater than min for each game type
            const gameTypes = ['digit_2', 'digit_3', 'digit_4', 'digit_rp2', 'digit_rp3'];
            
            gameTypes.forEach(type => {
                const minInput = document.querySelector(`input[name="min_${type}"]`);
                const maxInput = document.querySelector(`input[name="max_${type}"]`);
                
                if (minInput && maxInput && minInput.value && maxInput.value) {
                    const minValue = parseFloat(minInput.value);
                    const maxValue = parseFloat(maxInput.value);
                    
                    if (maxValue < minValue) {
                        alert(`Maximum bet must be greater than minimum bet for ${type.replace('digit_', '').replace('rp', 'PL').toUpperCase()}`);
                        isValid = false;
                    }
                }
            });
            
            return isValid;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            togglePackageInputs();
            
            // Add form validation on submit
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-admin>