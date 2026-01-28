@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Chỉnh Sửa Thành Viên</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.team.update', $team) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tên đầy đủ *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $team->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Initials (2 ký tự) *</label>
                            <input type="text" name="initials" class="form-control @error('initials') is-invalid @enderror" 
                                   value="{{ old('initials', $team->initials) }}" maxlength="2" required>
                            @error('initials')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Màu badge *</label>
                            <input type="color" name="avatar_color" class="form-control form-control-color @error('avatar_color') is-invalid @enderror" 
                                   value="{{ old('avatar_color', $team->avatar_color) }}" required>
                            @error('avatar_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Chức vụ *</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $team->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả *</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $team->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Avatar</label>
                            
                            @if($team->avatar)
                                <div class="mb-2">
                                    <img src="{{ $team->avatar_url }}" class="rounded" width="100" alt="Current avatar">
                                    <p class="text-muted small mt-1">Avatar hiện tại</p>
                                </div>
                            @else
                                <div class="mb-2">
                                    <div class="rounded d-inline-flex align-items-center justify-content-center" 
                                         style="width: 100px; height: 100px; background-color: {{ $team->avatar_color }}; color: white; font-weight: bold; font-size: 2rem;">
                                        {{ $team->initials }}
                                    </div>
                                    <p class="text-muted small mt-1">Initials badge hiện tại</p>
                                </div>
                            @endif
                            
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Upload ảnh mới để thay thế. Max: 2MB</small>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Thứ tự hiển thị *</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                                   value="{{ old('order', $team->order) }}" min="0" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Trạng thái</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Hiển thị</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
