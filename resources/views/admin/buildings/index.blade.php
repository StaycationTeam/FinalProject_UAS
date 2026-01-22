@extends('admin.layouts.app')

@section('title', 'Manage Buildings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-building"></i> Manage Buildings</h2>
    <a href="{{ route('admin.buildings.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Building
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Gold Cost</th>
                        <th>Level</th>
                        <th>Production</th>
                        <th>Defense</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buildings as $building)
                        <tr>
                            <td>{{ $building->id }}</td>
                            <td><strong>{{ $building->name }}</strong></td>
                            <td><span class="badge bg-info">{{ $building->type }}</span></td>
                            <td><i class="fas fa-coins text-warning"></i> {{ $building->gold_cost }}</td>
                            <td>Lv. {{ $building->level }}</td>
                            <td>
                                @if($building->gold_production > 0)
                                    <span class="badge bg-warning text-dark">Gold: {{ $building->gold_production }}</span>
                                @endif
                                @if($building->troop_production > 0)
                                    <span class="badge bg-success">Troops: {{ $building->troop_production }}</span>
                                @endif
                            </td>
                            <td>
                                @if($building->defense_bonus > 0)
                                    <i class="fas fa-shield-alt text-primary"></i> {{ $building->defense_bonus }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.buildings.toggle', $building) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $building->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $building->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.buildings.edit', $building) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.buildings.destroy', $building) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this building?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No buildings found. Create your first building!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $buildings->links() }}
        </div>
    </div>
</div>
@endsection
