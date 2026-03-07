@extends('ipal::layouts.main')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">Upload GeoJSON</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('ipal.upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Data Type</label>
                            <select name="tipe" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <option value="manhole">Manhole</option>
                                <option value="pipe">Pipe (Jaringan Pipa)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">GeoJSON File</label>
                            <input type="file" name="file" class="form-control" accept=".geojson,.json" required />
                            <div class="form-text">Accepted formats: .geojson, .json (max 50MB)</div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Upload History</h3>
            </div>
            <div class="card-body">
                <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>ID</th>
                            <th>Type</th>
                            <th>Original File</th>
                            <th>Features</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($uploads as $upload)
                        <tr>
                            <td>{{ $upload->id }}</td>
                            <td><span class="badge badge-light-primary">{{ $upload->tipe }}</span></td>
                            <td>{{ $upload->nama_file_asli }}</td>
                            <td>{{ $upload->total_fitur ?? '-' }}</td>
                            <td>
                                @if($upload->status === 'completed')
                                    <span class="badge badge-light-success">Completed</span>
                                @elseif($upload->status === 'failed')
                                    <span class="badge badge-light-danger" title="{{ $upload->pesan_error }}">Failed</span>
                                @elseif($upload->status === 'processing')
                                    <span class="badge badge-light-info">Processing</span>
                                @else
                                    <span class="badge badge-light-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $upload->user->name ?? '-' }}</td>
                            <td>{{ $upload->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <form action="{{ route('ipal.upload.destroy', $upload->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this upload and all associated data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No uploads yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($uploads->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $uploads->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
