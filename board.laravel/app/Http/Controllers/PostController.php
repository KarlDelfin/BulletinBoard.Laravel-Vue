<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Http\Logics\PaginationLogic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $currentPage = $request->input('currentPage', 1);
        $elementsPerPage = $request->input('elementsPerPage', 10);
        $search = $request->input('search', '');
        $select = $request->input('select', 'writer');

        $paginationRequest = [
            'currentPage' => $currentPage,
            'elementsPerPage' => $elementsPerPage
        ];

        $posts = Post::query();
        if($search != ''){
            $posts = $posts->where($select, 'LIKE', '%'.$search.'%');
        }
        $posts = $posts->get();
        $pagination = (new PaginationLogic())->paginateData($posts, $paginationRequest);
        return response()->json($pagination, 200);
    }

    public function store(Request $request)
    {
        $base64 = base64_decode($request->file);
        $file_name = date('YmdHis').'.jpg';
        file_put_contents($file_name, $base64);

        $data = [
            'writer' => $request->writer,
            'subject' => $request->subject,
            'message' => $request->message,
            'file' => $file_name,
        ];

        Post::create($data);

        return response()->json('Post created successfully', 200);

    }

    public function show(string $id)
    {
        $post = Post::find($id);
        if(!$post){
            return response()->json('Post not found', 404);
        }

        $filePath = public_path($post->file);
        $fileContent = file_get_contents($filePath); // convert image to base64
        $mimeType = mime_content_type($filePath); // get file extension type
        $base64 = base64_encode($fileContent); // convert image to base64

        $dataUri = 'data:' . $mimeType . ';base64,' . $base64;

        $data = [
            'writer' => $post->writer,
            'subject' => $post->subject,
            'message' => $post->message,
            'file' => $dataUri
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if(!$post){
            return response()->json('Post not found', 404);
        }

        if($request->file == ''){
            $data = [
                'writer' => $request->writer,
                'subject' => $request->subject,
                'message' => $request->message,
            ];
            $post->update($data);
            return response()->json('Post updated successfully', 200);
        }

        if($request->file != ''){
            unlink(public_path($post->file)); // delete file
            $base64 = base64_decode($request->file);
            $file_name = date('YmdHis').'.jpg';
            file_put_contents($file_name, $base64);
            $data = [
                'writer' => $request->writer,
                'subject' => $request->subject,
                'message' => $request->message,
                'file' => $file_name
            ];
            $post->update($data);
            return response()->json('Post updated successfully', 200);
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        Post::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Posts deleted successfully.'], 200);

    }
}
