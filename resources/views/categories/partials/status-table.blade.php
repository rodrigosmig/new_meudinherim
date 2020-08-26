<tr>
    <td>{{ $category->name }}</td>
    <td class="table-actions">
        <div class="row">
            <a class="btn btn-info btn-sm edit" href="{{ route('categories.edit', $category->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <button class="btn btn-danger btn-sm delete-category" data-category="{{ $category->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>