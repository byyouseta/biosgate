@extends('layouts.master')


@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <h2 class="card-title">Role Management</h2> --}}
                            @can('role-create')
                                <a class="btn btn-primary btn-sm" href="{{ route('roles.create') }}">Buat Grub Akses</a>
                            @endcan
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Grup</th>
                                    <th width="280px">Action</th>
                                </tr>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>

                                            {{-- <a class="btn btn-success btn-sm"
                                                href="{{ route('roles.show', $role->id) }}">Lihat</a> --}}
                                            @can('role-edit')
                                                <a class="btn btn-warning btn-sm"
                                                    href="{{ route('roles.edit', $role->id) }}">Edit</a>
                                            @endcan
                                            @can('role-delete')
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                                                {!! Form::submit('Hapus', ['class' => 'btn btn-danger btn-sm']) !!}
                                                {!! Form::close() !!}
                                            @endcan

                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>

                {{-- @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif --}}

                {!! $roles->render() !!}

            </div>
    </section>
@endsection
