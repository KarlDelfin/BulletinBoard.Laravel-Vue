<?php
namespace App\Http\Controllers;
use App\Models\Post;
use App\Http\Logics\PaginationLogic;
use App\Http\Logics\ImageLogic;
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
        $select = $request->input('select', 'subject');

        $paginationRequest = [
            'currentPage' => $currentPage,
            'elementsPerPage' => $elementsPerPage
        ];

        $posts = Post::query();
        $posts = $posts->orderBy('date_time_created', 'DESC');
        if($search != ''){
            $posts = $posts->where($select, 'LIKE', '%'.$search.'%');
        }

        $posts = $posts->get();
        $pagination = (new PaginationLogic())->paginateData($posts, $paginationRequest);
        return response()->json($pagination, 200);
    }

    public function store(Request $request)
    {
        $fileExtension = $request->fileExtension; // file extension
        $base64 = base64_decode($request->file); // base64 to file
        $fileName = date('YmdHis').'.'.$fileExtension; // create new file name
        $filePath = public_path('images/'. $fileName); // file path to store
        file_put_contents($filePath, $base64); // put image to path

        $data = [
            'writer' => $request->writer,
            'subject' => $request->subject,
            'message' => $request->message,
            'file' => $fileName,
        ];
        Post::create($data);
        return response()->json('Post created successfully', 200);

    }

    public function show(string $id)
    {
        $filePath = public_path('images/'.$post->file);
        $post = Post::find($id);
        if(!$post){
            return response()->json('Post not found', 404);
        }

        if (!file_exists($filePath)) { // check if file does exists
            $data = [
                'writer' => $post->writer,
                'subject' => $post->subject,
                'message' => $post->message,
                'file' => 'File deleted'
            ];
            return response()->json($data, 200);
        }

        $fileContent = file_get_contents($filePath); // convert image to base64
        $fileExtension = mime_content_type($filePath); // get file extension type
        $base64 = base64_encode($fileContent); // convert image to base64
        $dataUrl = 'data:' . $fileExtension . ';base64,' . $base64; // construct base64 string
        $fileExtension = pathinfo($post->file, PATHINFO_EXTENSION);
        if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) { // resize if image
            $resizeImage = (new ImageLogic())->resizeBase64Image($dataUrl);
        }

        $data = [
            'writer' => $post->writer,
            'subject' => $post->subject,
            'message' => $post->message,
            'file' => $resizeImage ?? $dataUrl
        ];
        return response()->json($data, 200);
    }

    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        if(!$post){
            return response()->json('Post not found', 404);
        }

        if($request->file == ''){ // check if user does not choose file
            $data = [
                'writer' => $request->writer,
                'subject' => $request->subject,
                'message' => $request->message,
            ];
            $post->update($data);
            return response()->json('Post updated successfully', 200);
        }

        // check if user choose file
        unlink(public_path('images/'.$post->file)); // delete file
        $fileExtension = $request->fileExtension; // file extension
        $base64 = base64_decode($request->file);
        $fileName = date('YmdHis').'.'.$fileExtension;
        $filePath = public_path('images/'.$fileName);
        file_put_contents($filePath, $base64);

        $data = [
            'writer' => $request->writer,
            'subject' => $request->subject,
            'message' => $request->message,
            'file' => $fileName
        ];

        $post->update($data);
        return response()->json('Post updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        $posts = Post::whereIn('id', $ids)->get(); // to array

        foreach($posts as $post){ // then loop to delete files
            $filePath = public_path('images/'.$post->file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        Post::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Posts deleted successfully.'], 200);

    }
}
