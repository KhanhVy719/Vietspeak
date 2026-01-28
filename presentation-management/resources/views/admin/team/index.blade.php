@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>Quản Lý Thành Viên</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm Thành Viên
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">Avatar</th>
                        <th width="60">Initials</th>
                        <th>Tên</th>
                        <th>Chức vụ</th>
                        <th width="80">Thứ tự</th>
                        <th width="100">Trạng thái</th>
                        <th width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td>
                            @if($member->avatar)
                                <img src="{{ $member->avatar_url }}" class="rounded-circle" width="40" height="40" alt="{{ $member->name }}">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; background-color: {{ $member->avatar_color }}; color: white; font-weight: bold;">
                                    {{ $member->initials }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $member->initials }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ Str::limit($member->title, 40) }}</td>
                        <td>{{ $member->order }}</td>
                        <td>
                            @if($member->is_active)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.team.edit', $member) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <form action="{{ route('admin.team.destroy', $member) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Chưa có thành viên nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
