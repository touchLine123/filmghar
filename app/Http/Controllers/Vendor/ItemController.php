<?php

namespace App\Http\Controllers\Vendor;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Lib\MultiVideoUploader;
use App\Models\Category;
use App\Models\Episode;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\Subtitle;
use App\Models\User;
use App\Models\Video;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller {

    public function items() {
        $pageTitle = "Video Items";
        $items     = $this->itemsData('AllItems');
        return view('vendor.item.index', compact('pageTitle', 'items'));
    }

    public function singleItems() {
        $pageTitle = "Single Video Items";
        $items     = $this->itemsData('singleItems');
        return view('vendor.item.index', compact('pageTitle', 'items'));
    }

    public function episodeItems() {
        $pageTitle = "Episode Video Items";
        $items     = $this->itemsData('episodeItems');
        return view('vendor.item.index', compact('pageTitle', 'items'));
    }

    public function trailerItems() {
        $pageTitle = "Trailer Video Items";
        $items     = $this->itemsData('trailerItems');
        return view('vendor.item.index', compact('pageTitle', 'items'));
    }
    public function rentItems() {
        $pageTitle = "Rent Video Items";
        $items     = $this->itemsData('rentItems');
        return view('vendor.item.index', compact('pageTitle', 'items'));
    }

    private function itemsData($scope = null) {
        if ($scope) {
            $items = Item::$scope()->with('category', 'sub_category', 'video');
        } else {
            $items = Item::with('category', 'sub_category', 'video');
        }
        $items = $items->searchable(['title', 'category:name'])->orderBy('id', 'desc')->paginate(getPaginate());
        return $items;
    }

    public function create() {
        $pageTitle  = "Add New Item";
        $categories = Category::active()->with(['subcategories' => function ($subcategory) {
            $subcategory->active();
        },
        ])->orderBy('id', 'desc')->get();
        return view('vendor.item.create', compact('pageTitle', 'categories'));
    }

    public function store(Request $request) {
        $this->itemValidation($request, 'create');
        $team = [
            'director' => implode(',', $request->director),
            'producer' => implode(',', $request->producer),
            'casts'    => implode(',', $request->casts),
            'genres'   => implode(',', $request->genres),
            'language' => implode(',', $request->language),
        ];

        $item  = new Item();
        $image = $this->imageUpload($request, $item, 'update');

        $item->item_type = $request->item_type;
        $item->slug      = slug($request->title . '-' . now()->format('dmhis') . getTrx(5));
        $item->featured  = 0;

        $this->saveItem($request, $team, $image, $item);

        $notify[] = ['success', 'Item added successfully'];
        if ($request->item_type == Status::EPISODE_ITEM) {
            return redirect()->route('vendor.item.episodes', $item->id)->withNotify($notify);
        } else {
            return redirect()->route('vendor.item.uploadVideo', $item->id)->withNotify($notify);
        }
    }

    public function edit($id) {
        $item       = Item::findOrFail($id);
        $pageTitle  = "Edit : " . $item->title;
        $categories = Category::active()->with(['subcategories' => function ($subcategory) {
            $subcategory->where('status', 1);
        },
        ])->orderBy('id', 'desc')->get();
        $subcategories = SubCategory::where('status', 1)->where('category_id', $item->category_id)->orderBy('id', 'desc')->get();
        return view('vendor.item.edit', compact('pageTitle', 'item', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id) {
        $this->itemValidation($request, 'update');

        $team = [
            'director' => implode(',', $request->director),
            'producer' => implode(',', $request->producer),
            'casts'    => implode(',', $request->casts),
            'genres'   => implode(',', $request->genres),
            'language' => implode(',', $request->language),
        ];

        $item             = Item::findOrFail($id);
        $item->single     = $request->single ? Status::ENABLE : Status::DISABLE;
        // $item->status     = $request->status ? Status::ENABLE : Status::DISABLE;
        $item->trending   = $request->trending ? Status::ENABLE : Status::DISABLE;
        $item->featured   = $request->featured ? Status::ENABLE : Status::DISABLE;
        $item->is_trailer = $request->is_trailer ? Status::ENABLE : Status::DISABLE;
        $image            = $this->imageUpload($request, $item, 'update');
        $item->release_date           = $request->release_date;
        $item->movie_duration_hours   = $request->movie_duration_hours;
        $item->movie_duration_minutes = $request->movie_duration_minutes;
        $this->saveItem($request, $team, $image, $item);

        $notify[] = ['success', 'Item updated successfully'];
        return back()->withNotify($notify);
    }

    private function itemValidation($request, $type) {
        $validation = $type == 'create' ? 'required' : 'nullable';
        $versions   = implode(',', [Status::FREE_VERSION, Status::PAID_VERSION, Status::RENT_VERSION]);
        $request->validate([
            'title'           => 'required|string|max:255',
            'category'        => 'required|integer|exists:categories,id',
            // 'sub_category_id' => 'nullable|integer|exists:sub_categories,id',
            'preview_text'    => 'required|string|max:255',
            'description'     => 'required|string',
            'director'        => 'required',
            'producer'        => 'required',
            'casts'           => 'required',
            'tags'            => 'required',
            'item_type'       => "$validation|in:1,2",
            'version'         => "nullable|required_if:item_type,==,1|in:$versions",
            'ratings'         => 'required|numeric',
            'rent_price'      => 'required_if:version,==,2|nullable|numeric|gte:0',
            'rental_period'   => 'required_if:version,==,2|nullable|integer|gte:0',
            'exclude_plan'    => 'required_if:version,==,2|nullable|in:0,1',
        ]);
    }

    private function imageUpload($request, $item, $type) {
        $landscape = @$item->image->landscape;
        $portrait  = @$item->image->portrait;

        if ($request->landscape_url) {
            $url      = $request->landscape_url;
            $contents = file_get_contents($url);
            $name     = substr($url, strrpos($url, '/') + 1);
            fileManager()->makeDirectory(getFilePath('item_landscape'));
            $path = getFilePath('item_landscape') . $name;
            Storage::put($name, $contents);
            File::move(storage_path('app/' . $name), $path);
            $landscape = $name;
        }

        if ($request->hasFile('landscape')) {
            $maxLandScapSize = $request->landscape->getSize() / 3000000;

            if ($maxLandScapSize > 3) {
                throw ValidationException::withMessages(['landscape' => 'Landscape image size could not be greater than 3mb']);
            }

            try {
                $date = date('Y') . '/' . date('m') . '/' . date('d');
                $type == 'update' ? fileManager()->removeFile(getFilePath('item_landscape') . @$item->image->landscape) : '';
                $landscape = $date . '/' . fileUploader($request->landscape, getFilePath('item_landscape') . $date);
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['landscape' => 'Landscape image could not be uploaded']);
            }

        }

        if ($request->portrait_url) {
            $url      = $request->portrait_url;
            $contents = file_get_contents($url);
            $name     = substr($url, strrpos($url, '/') + 1);
            fileManager()->makeDirectory(getFilePath('item_portrait'));
            $path = getFilePath('item_portrait') . $name;
            Storage::put($name, $contents);
            File::move(storage_path('app/' . $name), $path);
            $portrait = $name;
        }

        if ($request->hasFile('portrait')) {
            $maxLandScapSize = $request->portrait->getSize() / 3000000;

            if ($maxLandScapSize > 3) {
                throw ValidationException::withMessages(['portrait' => 'Portrait image size could not be greater than 3mb']);
            }

            try {
                $date = date('Y') . '/' . date('m') . '/' . date('d');
                $type == 'update' ? fileManager()->removeFile(getFilePath('item_portrait') . @$item->image->portrait) : '';
                $portrait = $date . '/' . fileUploader($request->portrait, getFilePath('item_portrait') . $date);
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['portrait' => 'Portrait image could not be uploaded']);
            }

        }

        $image = [
            'landscape' => $landscape,
            'portrait'  => $portrait,
        ];
        return $image;
    }

    private function saveItem($request, $team, $image, $item) {
        $admin     = auth('vendor')->user();
        $item->category_id     = $request->category;
        $item->sub_category_id = $request->sub_category_id;
        $item->title           = $request->title;
        $item->preview_text    = $request->preview_text;
        $item->description     = $request->description;
        $item->status          = 0;
        $item->release_date           = $request->release_date;
        $item->movie_duration_hours   = $request->movie_duration_hours;
        $item->movie_duration_minutes = $request->movie_duration_minutes;
        $item->team            = $team;
        $item->tags            = implode(',', $request->tags);
        $item->image           = $image;
        $item->version         = @$request->version ?? 0;
        $item->ratings         = $request->ratings;
        $item->addedBy         = $admin->id;
        if ($request->version == Status::RENT_VERSION || $request->rent == Status::RENT_VERSION) {
            $item->rent_price    = @$request->rent_price ?? 0;
            $item->rental_period = @$request->rental_period ?? 0;
            $item->exclude_plan  = @$request->exclude_plan ?? 1;
        } else {
            $item->rent_price    = 0;
            $item->rental_period = 0;
            $item->exclude_plan  = 1;
        }

        $item->save();

    }

    public function uploadVideo($id) {
        $item  = Item::findOrFail($id);
        $video = $item->video;

        if ($video) {
            $notify[] = ['error', 'Already video exist'];
            return back()->withNotify($notify);
        }

        $pageTitle = "Upload video to: " . $item->title;
        $prevUrl   = route('vendor.item.index');
        $route     = route('vendor.item.upload.video', $item->id);
        return view('vendor.item.video.upload', compact('item', 'pageTitle', 'video', 'prevUrl', 'route'));
    }

    public function upload(Request $request, $id) {

        $item = Item::where('id', $id)->first();
        if (!$item) {
            return response()->json(['error' => 'Item not found']);
        }

        $video = $item->video;

        if ($video) {
            $sevenTwentyLink  = 'nullable';
            $sevenTwentyVideo = 'nullable';
        } else {
            $sevenTwentyLink  = 'required_if:video_type_seven_twenty,0';
            $sevenTwentyVideo = 'required_if:video_type_seven_twenty,1';
        }

        ini_set('memory_limit', '-1');
        $validator = Validator::make($request->all(), [
            'video_type_three_sixty'     => 'required',
            'three_sixty_link'           => 'nullable',
            'three_sixty_video'          => ['nullable', new FileTypeValidate(['mp4', 'mkv', '3gp'])],

            'video_type_four_eighty'     => 'required',
            'four_eighty_link'           => 'nullable',
            'four_eighty_video'          => ['nullable', new FileTypeValidate(['mp4', 'mkv', '3gp'])],

            'video_type_seven_twenty'    => 'required',
            'seven_twenty_link'          => "$sevenTwentyLink",
            'seven_twenty_video'         => ["$sevenTwentyVideo", new FileTypeValidate(['mp4', 'mkv', '3gp'])],

            'video_type_thousand_eighty' => 'required',
            'thousand_eighty_link'       => 'nullable',
            'thousand_eighty_video'      => ['nullable', new FileTypeValidate(['mp4', 'mkv', '3gp'])],
        ], [
            'video_type_three_sixty'     => 'Video file 360P type is required',
            'three_sixty_link'           => 'Video file 360P link is required',
            'three_sixty_video'          => 'Video file 360P video is required',
            'video_type_four_eighty'     => 'Video file 480P type is required',
            'four_eighty_link'           => 'Video file 480P link is required',
            'four_eighty_video'          => 'Video file 480P video is required',
            'video_type_seven_twenty'    => 'Video file 720P type is required',
            'seven_twenty_link'          => 'Video file 720P link is required',
            'seven_twenty_video'         => 'Video file 720P video is required',
            'video_type_thousand_eighty' => 'Video file 1080P type is required',
            'thousand_eighty_link'       => 'Video file 1080P link is required',
            'thousand_eighty_video'      => 'Video file 1080P video is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $sizeValidation = MultiVideoUploader::checkSizeValidation();
        if ($sizeValidation['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        $uploadThreeSixty = MultiVideoUploader::multiQualityVideoUpload($video, 'three_sixty');
        if ($uploadThreeSixty['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        $uploadFourEighty = MultiVideoUploader::multiQualityVideoUpload($video, 'four_eighty');
        if ($uploadFourEighty['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        $uploadSevenTwenty = MultiVideoUploader::multiQualityVideoUpload($video, 'seven_twenty');
        if ($uploadSevenTwenty['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        $uploadThousandEighty = MultiVideoUploader::multiQualityVideoUpload($video, 'thousand_eighty');
        if ($uploadThousandEighty['error']) {
            return response()->json(['error' => $sizeValidation['message']]);
        }

        if (!$video) {
            $video          = new Video();
            $video->item_id = $item->id;
        }

        $video->video_type_three_sixty     = @$request->video_type_three_sixty;
        $video->video_type_four_eighty     = @$request->video_type_four_eighty;
        $video->video_type_seven_twenty    = @$request->video_type_seven_twenty;
        $video->video_type_thousand_eighty = @$request->video_type_thousand_eighty;

        $video->three_sixty_video     = @$uploadThreeSixty['three_sixty_video'];
        $video->four_eighty_video     = @$uploadFourEighty['four_eighty_video'];
        $video->seven_twenty_video    = @$uploadSevenTwenty['seven_twenty_video'];
        $video->thousand_eighty_video = @$uploadThousandEighty['thousand_eighty_video'];

        $video->server_three_sixty     = @$uploadThreeSixty['server'] ?? 0;
        $video->server_four_eighty     = @$uploadFourEighty['server'] ?? 0;
        $video->server_seven_twenty    = @$uploadSevenTwenty['server'] ?? 0;
        $video->server_thousand_eighty = @$uploadThousandEighty['server'] ?? 0;

        $video->save();
        return response()->json(['success' => 'Video uploaded successfully']);
    }

    public function updateVideo(Request $request, $id) {
        $item  = Item::findOrFail($id);
        $video = $item->video;

        if (!$video) {
            $notify[] = ['error', 'Video not found'];
            return back()->withNotify($notify);
        }

        $pageTitle   = "Update video of: " . $item->title;
        $posterImage = getImage(getFilePath('item_landscape') . @$item->image->landscape);
        $general     = gs();

        $videoFile['three_sixty']     = getVideoFile($video, 'three_sixty');
        $videoFile['four_eighty']     = getVideoFile($video, 'four_eighty');
        $videoFile['seven_twenty']    = getVideoFile($video, 'seven_twenty');
        $videoFile['thousand_eighty'] = getVideoFile($video, 'thousand_eighty');

        $route   = route('vendor.item.upload.video', @$item->id);
        $prevUrl = route('vendor.item.index');
        return view('vendor.item.video.update', compact('item', 'pageTitle', 'video', 'videoFile', 'posterImage', 'prevUrl', 'route'));
    }

    public function itemList(Request $request) {
        $items = Item::hasVideo();

        if (request()->search) {
            $items = $items->where('title', 'like', "%$request->search%");
        }

        $items = $items->latest()->paginate(getPaginate());

        foreach ($items as $item) {
            $response[] = [
                'id'   => $item->id,
                'text' => $item->title,
            ];
        }

        return $response ?? [];
    }

    public function itemFetch(Request $request) {
        $validate = Validator::make($request->all(), [
            'id'        => 'required|integer',
            'item_type' => 'required|integer|in:1,2',
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()]);
        }
        $general  = gs();
        $itemType = $request->item_type == 1 ? 'movie' : 'tv';
        $tmDbUrl  = 'https://api.themoviedb.org/3/' . $itemType . '/' . $request->id;
        $url      = $tmDbUrl . '?api_key=' . $general->tmdb_api;
        $castUrl  = $tmDbUrl . '/credits?api_key=' . $general->tmdb_api;
        $tags     = $tmDbUrl . '/keywords?api_key=' . $general->tmdb_api;

        $movieResponse = CurlRequest::curlContent($url);
        $castResponse  = CurlRequest::curlContent($castUrl);
        $tagsResponse  = CurlRequest::curlContent($tags);

        $data  = json_decode($movieResponse);
        $casts = json_decode($castResponse);
        $tags  = json_decode($tagsResponse);

        if (isset($data->success)) {
            return response()->json(['error' => 'The resource you requested could not be found.']);
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
            'casts'   => $casts,
            'tags'    => $tags,
        ]);
    }

    public function sendNotification($id) {
        $item      = Item::where('status', 1)->findOrFail($id);
        $clickUrl  = route('watch', $item->slug);
        $users     = User::active()->cursor();
        $shortCode = [
            'title' => $item->title,
        ];

        $user = User::find(2252);
        notify($user, 'SEND_ITEM_NOTIFICATION', $shortCode, redirectUrl: $clickUrl);

        // foreach ($users as $user) {
        // }
        $notify[] = ['success', 'Notification send successfully'];
        return back()->withNotify($notify);

    }

    public function adsDuration($id, $episodeId = 0) {
        $pageTitle = 'Ads Configuration';
        $item      = Item::findOrFail($id);

        if ($item->item_type == 1 || $item->item_type == 3) {
            $episodeId = null;
            $video     = $item->video;
        } else {
            $episode   = $item->episodes()->where('id', $episodeId)->with('video')->first();
            $video     = $episode->video;
            $episodeId = $episode->id;
        }
        $general   = gs();
        $videoFile = getVideoFile($video, 'seven_twenty');
        return view('vendor.item.video.ads', compact('pageTitle', 'item', 'video', 'videoFile', 'episodeId'));
    }

    public function adsDurationStore(Request $request, $id = 0, $episodeId = 0) {
        $request->validate([
            'ads_time'   => 'required|array',
            'ads_time.*' => 'required',
        ]);
        $item = Item::findOrFail($id);
        if ($item->item_type == 1 || $item->item_type == 3) {
            $video = $item->video;
        } else {
            $episode = $item->episodes()->with('video')->findOrFail($episodeId);
            $video   = $episode->video;
        }
        for ($i = 0; $i < count($request->ads_time); $i++) {
            $arr = explode(':', $request->ads_time[$i]);
            if (count($arr) === 3) {
                $second[] = $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
            } else {
                $second[] = $arr[0] * 60 + $arr[1];
            }
        }
        $video->seconds  = $second;
        $video->ads_time = $request->ads_time;
        $video->save();

        $notify[] = ['success', 'Ads time added successfully'];
        return back()->withNotify($notify);

    }

    public function subtitles($id, $videoId = 0) {
        $itemId    = 0;
        $episodeId = 0;
        if ($videoId == 0) {
            $item      = Item::with('video')->findOrFail($id);
            $videoId   = $item->video->id;
            $columName = 'item_id';
            $itemId    = $item->id;
        } else {
            $item      = Episode::with('video')->findOrFail($id);
            $columName = 'episode_id';
            $episodeId = $item->id;
        }
        $subtitles = Subtitle::where($columName, $id)->where('video_id', $videoId)->paginate(getPaginate());
        $pageTitle = 'Subtitles for - ' . $item->title;
        return view('vendor.item.video.subtitles', compact('pageTitle', 'item', 'subtitles', 'videoId', 'episodeId', 'itemId'));
    }

    public function subtitleStore(Request $request, $itemId, $episodeId, $videoId, $id = 0) {
        $validate = $id ? 'nullable' : 'required';
        $request->validate([
            'language' => 'required|string|max:40',
            'code'     => 'required|string|max:40',
            'file'     => [$validate, new FileTypeValidate(['vtt'])],
        ]);

        if ($id) {
            $subtitle     = Subtitle::findOrFail($id);
            $oldFile      = $subtitle->file;
            $notification = 'Subtitle updated successfully';
        } else {
            $subtitle     = new Subtitle();
            $notification = 'Subtitle created successfully';
            $oldFile      = null;
        }

        $subtitle->item_id    = $itemId;
        $subtitle->episode_id = $episodeId;
        $subtitle->video_id   = $videoId;
        $subtitle->language   = $request->language;
        $subtitle->code       = strtolower($request->code);
        if ($request->file) {
            $subtitle->file = fileUploader($request->file, getFilePath('subtitle'), null, $oldFile);
        }
        $subtitle->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function subtitleDelete($id) {
        $subtitle = Subtitle::where('id', $id)->firstOrFail();
        fileManager()->removeFile(getFilePath('subtitle') . '/' . $subtitle->file);
        $subtitle->delete();
        $notify[] = ['success', 'Subtitle deleted successfully'];
        return back()->withNotify($notify);
    }

    public function report($id, $videoId = 0) {
        if ($videoId == 0) {
            $item        = Item::with('videoReport')->findOrFail($id);
            $videoReport = $item->videoReport;
            $title       = $item->title;
        } else {
            $episode     = Episode::with('item', 'videoReport')->findOrFail($videoId);
            $item        = $episode->item;
            $videoReport = $episode->videoReport;
            $title       = $episode->title;
        }

        $totalViews = $videoReport->count();
        $reports    = collect($videoReport)->groupBy(function ($data) {
            return substr($data['created_at'], 0, 10);
        })->map(function ($group) {
            return count($group);
        })->all();

        $pageTitle = 'Report - ' . $item->title;
        return view('vendor.item.report', compact('pageTitle', 'reports', 'item', 'totalViews', 'title'));
    }

}
