@extends('admin.layouts.app')

@section('title', 'Edit Building')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.buildings.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Buildings
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-warning">
        <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Building: {{ $building->name }}</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.buildings.update', $building->id) }}" method="POST" id="editBuildingForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Building Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $building->name) }}" 
                           required
                           maxlength="255">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Building Type <span class="text-danger">*</span></label>
                    <select class="form-control @error('type') is-invalid @enderror" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">Select Type</option>
                        <option value="main" {{ old('type', $building->type) == 'main' ? 'selected' : '' }}>Main Building</option>
                        <option value="barracks" {{ old('type', $building->type) == 'barracks' ? 'selected' : '' }}>Barracks</option>
                        <option value="mine" {{ old('type', $building->type) == 'mine' ? 'selected' : '' }}>Gold Mine</option>
                        <option value="walls" {{ old('type', $building->type) == 'walls' ? 'selected' : '' }}>Defense Walls</option>
                        <option value="other" {{ old('type', $building->type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3" 
                          required>{{ old('description', $building->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="gold_cost" class="form-label">Gold Cost <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('gold_cost') is-invalid @enderror" 
                           id="gold_cost" 
                           name="gold_cost" 
                           value="{{ old('gold_cost', $building->gold_cost) }}" 
                           min="0" 
                           required>
                    @error('gold_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('level') is-invalid @enderror" 
                           id="level" 
                           name="level" 
                           value="{{ old('level', $building->level) }}" 
                           min="1" 
                           required>
                    @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="gold_production" class="form-label">Gold Production</label>
                    <input type="number" 
                           class="form-control @error('gold_production') is-invalid @enderror" 
                           id="gold_production" 
                           name="gold_production" 
                           value="{{ old('gold_production', $building->gold_production ?? 0) }}" 
                           min="0">
                    @error('gold_production')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="troop_production" class="form-label">Troop Production</label>
                    <input type="number" 
                           class="form-control @error('troop_production') is-invalid @enderror" 
                           id="troop_production" 
                           name="troop_production" 
                           value="{{ old('troop_production', $building->troop_production ?? 0) }}" 
                           min="0">
                    @error('troop_production')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="defense_bonus" class="form-label">Defense Bonus</label>
                    <input type="number" 
                           class="form-control @error('defense_bonus') is-invalid @enderror" 
                           id="defense_bonus" 
                           name="defense_bonus" 
                           value="{{ old('defense_bonus', $building->defense_bonus ?? 0) }}" 
                           min="0">
                    @error('defense_bonus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4 pt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $building->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <strong>Active</strong> (Available for players to build)
                        </label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.buildings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-warning" id="submitBtn">
                    <i class="fas fa-save"></i> Update Building
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Ensure form submission works properly
document.getElementById('editBuildingForm').addEventListener('submit', function(e) {
    // Disable submit button to prevent double submission
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    // Allow form to submit normally
    return true;
});
</script>
@endpush
