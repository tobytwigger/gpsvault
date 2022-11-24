<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Route;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\Filepond\FilepondRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteFileController extends Controller
{
    public function destroy(Route $route, File $file)
    {
        $this->authorize('view', $route);
        $this->authorize('delete', $file);
        abort_if(!$route->files()->where('files.id', $file->id)->exists(), 404, 'The file is not attached to the route');

        if (!(
            $route->file_id === $file->id
            || $route->whereHas('files', fn (Builder $query) => $query->where('files.id', $file->id))->exists()
        )) {
            throw new NotFoundHttpException('The file is not attached to the route');
        }

        $file->delete();

        return redirect()->route('route.show', $route);
    }

    public function store(Request $request, Route $route)
    {
        $this->authorize('create', File::class);
        $this->authorize('update', $route);

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => [app(FilepondRule::class)],
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535',
        ]);

        $files = collect($request->input('files', []))
            ->map(function (array $file) use ($request) {
                $file = Upload::filePondFile($file, Auth::user(), FileUploader::ROUTE_MEDIA);
                $file->title = $request->input('title');
                $file->caption = $request->input('caption');
                $file->save();

                return $file;
            });
        $route->files()->sync($files->pluck('id'));

        return redirect()->route('route.show', $route);
    }

    public function update(Request $request, Route $route, File $file)
    {
        $this->authorize('update', $file);
        $this->authorize('update', $route);
        abort_if(!$route->files()->where('files.id', $file->id)->exists(), 404, 'The file is not attached to the route');

        $request->validate([
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535',
        ]);

        $file->title = $request->get('title', $file->title);
        $file->caption = $request->get('caption', $file->caption);

        $file->save();

        return redirect()->route('route.show', $route);
    }
}
