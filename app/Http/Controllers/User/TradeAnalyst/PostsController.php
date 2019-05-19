<?php

namespace App\Http\Controllers\User\TradeAnalyst;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TradeAnalyst\PostRequest;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Services\Core\DataListService;
use App\Services\Core\FileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    private $postRepository;

    public function __construct(PostInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index()
    {
        $conditions = [
            'posts.user_id' => Auth::id(),
        ];


        $searchFields = [
            ['posts.title', __('Title')],
        ];

        $orderFields = [
            ['posts.id', __('ID')],
            ['posts.title', __('Title')],
            ['posts.is_published', __('Publish Status')],
            ['posts.created_at', __('Created Date')],
        ];

        $select = ['posts.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'posts.user_id'];

        $query = $this->postRepository->paginateWithFilters($searchFields, $orderFields, $conditions, $select, $join);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Posts');

        return view('backend.posts.index', $data);
    }

    public function create()
    {
        $data['title'] = __('Create Post');
        return view('backend.posts.create', $data);
    }

    public function store(PostRequest $request)
    {
        $attributes = $request->only(['title', 'content', 'is_published']);
        $attributes['user_id'] = Auth::id();

        $path = config('commonconfig.path_post');
        $attributes['featured_image'] = app(FileUploadService::class)->upload($request->featured_image, $path, now()->timestamp, Auth::id(), '', null, $width = 400, $height = 400);

        if (!$attributes['featured_image']) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to upload featured image'));
        }

        if ($post = $this->postRepository->create($attributes)) {
            return redirect()->route('trade-analyst.posts.edit', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Post has been created successfully.'));
        }

        Storage::delete($path . '/' . $attributes['featured_image']);
        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create post.'));
    }

    public function edit($id)
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];
        $data['post'] = $this->postRepository->getFirstByConditions($conditions);

        abort_if(empty($data['post']), 401, __('Unauthorized access!'));

        $data['title'] = __('Edit Post');
        return view('backend.posts.edit', $data);
    }

    public function update(PostRequest $request, $id)
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];

        $post = $this->postRepository->getFirstByConditions($conditions);

        if (empty($post)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Post could not found.'));
        }

        $attributes = $request->only(['title', 'content', 'is_published']);

        if ($request->hasFile('featured_image')) {
            $path = config('commonconfig.path_post');
            $fileName = pathinfo($post->featured_image, PATHINFO_FILENAME);
            $attributes['featured_image'] = app(FileUploadService::class)->upload($request->featured_image, $path, $fileName, '', '', null, $width = 400, $height = 400);

            if (!$attributes['featured_image']) {
                return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to upload featured image'));
            }
        }

        if ($post = $this->postRepository->update($attributes, $post->id)) {
            return redirect()->route('trade-analyst.posts.edit', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Post has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update post.'));
    }

    public function destroy($id)
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];
        $post = $this->postRepository->getFirstByConditions($conditions);

        abort_if(empty($post), 404, __('Not Found!'));

        if ($this->postRepository->deleteById($post->id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The post has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete post.'));
    }

    public function toggleActiveStatus($id)
    {
        if ($updatedInstance = $this->postRepository->toggleStatusById($id, 'is_published')) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The post publish status has been changed successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to change publish status.'));
    }
}