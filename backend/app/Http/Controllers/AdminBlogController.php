<?php
namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller {
    public function index() {
        return response()->json(BlogPost::with('author')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:3072'
        ]);

        $data = $request->except(['cover_image', 'is_published']);
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['author_id'] = $request->user()->id ?? null;
        $data['is_published'] = $request->has('is_published') ? filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN) : true;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('blog', 'public');
            $data['cover_image'] = url('storage/' . $path);
        }

        $post = BlogPost::create($data);
        return response()->json($post->load('author'), 201);
    }

    public function update(Request $request, $id) {
        $post = BlogPost::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:3072'
        ]);

        $data = $request->except(['cover_image', 'is_published']);
        if ($request->title !== $post->title) {
            $data['slug'] = Str::slug($request->title) . '-' . time();
        }
        
        if ($request->has('is_published')) {
            $data['is_published'] = filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('blog', 'public');
            $data['cover_image'] = url('storage/' . $path);
        }

        $post->update($data);
        return response()->json($post->load('author'));
    }

    public function destroy($id) {
        BlogPost::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف المقال بنجاح']);
    }

    public function batchDeletePosts(Request $request) {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:blog_posts,id']);
        BlogPost::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف المقالات بنجاح']);
    }
}
