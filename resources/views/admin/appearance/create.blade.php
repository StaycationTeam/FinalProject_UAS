@extends('admin.layouts.app')

@section('title', 'Add Appearance Part')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.appearance.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Appearance Parts
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-plus"></i> Add New Appearance Part</h4>
    </div>
    <div class="card-body">
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

        <form action="{{ route('admin.appearance.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tribe_id" class="form-label">Tribe <span class="text-danger">*</span></label>
                    <select class="form-control @error('tribe_id') is-invalid @enderror" 
                            id="tribe_id" 
                            name="tribe_id" 
                            required>
                        <option value="">Select Tribe</option>
                        @foreach($tribes as $tribe)
                            <option value="{{ $tribe->id }}" {{ old('tribe_id') == $tribe->id ? 'selected' : '' }}>
                                {{ $tribe->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tribe_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="part_type" class="form-label">Part Type <span class="text-danger">*</span></label>
                    <select class="form-control @error('part_type') is-invalid @enderror" 
                            id="part_type" 
                            name="part_type" 
                            required>
                        <option value="">Select Part Type</option>
                        @foreach($partTypes as $type)
                            <option value="{{ $type }}" {{ old('part_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('part_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required
                       placeholder="e.g., Warrior Head Style 1">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
                <input type="file" 
                       class="form-control @error('image') is-invalid @enderror" 
                       id="image" 
                       name="image" 
                       accept="image/*"
                       required
                       onchange="previewImage(event)">
                <small class="text-muted">Max size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF, SVG</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-3" style="display: none;">
                    <label class="form-label">Preview:</label>
                    <div>
                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" 
                           class="form-control @error('display_order') is-invalid @enderror" 
                           id="display_order" 
                           name="display_order" 
                           value="{{ old('display_order', 0) }}" 
                           min="0">
                    <small class="text-muted">Lower numbers appear first</small>
                    @error('display_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Options</label>
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_default" 
                               name="is_default" 
                               value="1"
                               {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_default">
                            <strong>Set as Default</strong> for this tribe & part type
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <strong>Active</strong> (Available for players)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Optional description for this appearance part">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.appearance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Create Appearance Part
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

// Prevent double submission
document.getElementById('createForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
});
</script>
@endpush
